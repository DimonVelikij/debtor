<?php

namespace AppBundle\Admin;

use AppBundle\Admin\traits\UserTrait;
use AppBundle\Entity\Company;
use AppBundle\Entity\Street;
use AppBundle\Entity\User;
use AppBundle\Service\AddressBookValidator;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

class HouseAdmin extends AbstractAdmin
{
    use UserTrait;

    /**
     * @param string $context
     * @return QueryBuilder|\Sonata\AdminBundle\Datagrid\ProxyQueryInterface
     * @throws \Exception
     */
    public function createQuery($context = 'list')
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        if (!$user->isSuperAdmin()) {
            $query
                ->andWhere(
                    $query->expr()->eq('o.company', $user->getCompany()->getId())
                );
        }

        return $query;
    }

    /**
     * @param RouteCollection $collection
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('batch')
            ->remove('export')
            ->remove('delete');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        /** @var User $user */
        $user = $this->getUser();

        $datagridMapper
            ->add('id')
            ->add('number', null, [
                'label' =>  'Номер дома'
            ])
            ->add('street', null, [
                'label' =>  'Улица'
            ])
            ->add('street.city', null, [
                'label' =>  'Город'
            ])
        ;

        if ($user->isSuperAdmin()) {
            $datagridMapper
                ->add('company', null, [
                    'label' =>  'Управляющая компания'
                ]);
        }
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        /** @var User $user */
        $user = $this->getUser();

        $listMapper
            ->add('id')
            ->add('number', null, [
                'label' =>  'Номер дома'
            ])
            ->add('street', null, [
                'label' =>  'Улица'
            ])
            ->add('street.city', null, [
                'label' =>  'Город'
            ])
            ->add('company', $user->isSuperAdmin() ? null : 'text', [
                'label' =>  'Управляющая компания'
            ])
            ->add('_action', null, array(
                'label'     =>  'Действия',
                'actions'   => array(
                    'show'  => array(),
                    'edit'  => array(),
                ),
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var User $user */
        $user = $this->getUser();

        //получаем список улиц
        $streets = $this->getEntityManager()
            ->getRepository('AppBundle:Street')
            ->createQueryBuilder('s')
            ->orderBy('s.city')
            ->getQuery()
            ->getResult();

        $streetHelp = count($streets) ?
            "<span style='color: blue;'>Если в списке нет нужной улицы, необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_street_create')}'>добавить улицу</a> и обновить страницу</span>" :
            "<span style='color: red'>Список улиц пуст. Необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_street_create')}'>добавить улицу</a> и обновить страницу</span>";

        $streetChoice = [];

        /** @var Street $street */
        foreach ($streets as $street) {
            $streetChoice[$street->getCity()->getTitle() . ', ' . $street->getTitle()] = $street;
        }

        //получаем список управляющих компаний
        $companies = $this->getEntityManager()->getRepository('AppBundle:Company')->findAll();

        $companyHelp = '';
        $companyChoice = [];

        if (!count($companies)) {
            $companyHelp = '<span style="color: red;">Не удалось найти управляющие компании. Обратитесь к администратору</span>';
        } else {
            if ($user->isSuperAdmin()) {
                /** @var Company $company */
                foreach ($companies as $company) {
                    $companyChoice[$company->getTitle()] = $company;
                }
            } else {
                $companyChoice[$user->getCompany()->getTitle()] = $user->getCompany();
            }
        }

        $companyOptions = [
            'label'         =>  'Управляющая компания',
            'required'      =>  true,
            'choices'       =>  $companyChoice,
            'constraints'   =>  [
                new NotBlank(['message' => 'Укажите управлющую компанию'])
            ],
            'help'          =>  $companyHelp
        ];

        if (!$user->isSuperAdmin()) {
            $companyOptions['data'] = current($companyChoice);
        }

        $formMapper
            ->add('street', ChoiceType::class, [
                'label'         =>  'Город, улица',
                'required'      =>  true,
                'choices'       =>  $streetChoice,
                'group_by'      =>  function ($value, $key, $index) {
                    return $value->getCity()->getTitle();
                },
                'help'          =>  $streetHelp,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите название улицы'])
                ]
            ])
            ->add('company', ChoiceType::class, $companyOptions)
            ->add('number', TextType::class, [
                'label'         =>  'Номер дома'
            ])
        ;

        /** @var AddressBookValidator $addressBookValidator */
        $addressBookValidator = $this->getContainer()->get('app.service.address_book_validator');
        $formMapper->getFormBuilder()->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($addressBookValidator) {
            $error = $addressBookValidator->validateHouse($event->getData());
            if ($error) {
                $event->getForm()->get('number')->addError(new FormError($error));
            }
        });
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        /** @var User $user */
        $user = $this->getUser();

        $showMapper
            ->add('id')
            ->add('number', null, [
                'label' =>  'Номер дома'
            ])
            ->add('street', null, [
                'label' =>  'Улица'
            ])
            ->add('street.city', null, [
                'label' =>  'Город'
            ])
            ->add('company', $user->isSuperAdmin() ? null : 'text', [
                'label' =>  'Управляющая компания'
            ])
        ;
    }

    /**
     * @return null|\Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->getConfigurationPool()->getContainer();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private function getRouter()
    {
        return $this->getContainer()->get('router');
    }
}
