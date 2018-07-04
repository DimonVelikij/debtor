<?php

namespace AppBundle\Admin;

use AppBundle\Admin\traits\UserTrait;
use AppBundle\Entity\User;
use AppBundle\Validator\Constraints\HouseExist;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            $query->andWhere($query->expr()->eq('o.company', $user->getCompany()->getId()));
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
            ->add('mkd', null, [
                'label' =>  'МКД'
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
            ->add('mkd', null, [
                'label' =>  'МКД'
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

        $formMapper
            ->add('street', 'entity', [
                'label'         =>  'Город, улица',
                'required'      =>  true,
                'class'         =>  'AppBundle\Entity\Street',
                'group_by'      =>  'city',
                'help'          =>  "<span style='color: blue;'>Если в списке нет нужной улицы, необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_street_create')}'>добавить улицу</a> и обновить страницу</span>",
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите название улицы'])
                ]
            ])
            ->add('company', 'entity', [
                'label'         =>  'Управляющая компания',
                'required'      =>  true,
                'class'         =>  'AppBundle\Entity\Company',
                'query_builder' =>  function (EntityRepository $entityRepository) use ($user) {
                    $queryBuilder = $entityRepository->createQueryBuilder('company');

                    if (!$user->isSuperAdmin()) {
                        $queryBuilder
                            ->where('company.id = :company_id')
                            ->setParameter('company_id', $user->getCompany()->getId());
                    }

                    return $queryBuilder;
                }
            ])
            ->add('number', TextType::class, [
                'label'         =>  'Номер дома',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите номер дома']),
                    new HouseExist()
                ]
            ])
            ->add('mkd', 'entity', [
                'label'         =>  'МКД',
                'required'      =>  true,
                'class'         =>  'AppBundle\Entity\MKD',
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите МКД'])
                ],
                'help'          =>  "<span style='color: blue;'>Если в списке нет нужного МКД, необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_mkd_create')}'>добавить МКД</a> и обновить страницу</span>",
            ])
        ;
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
            ->add('mkd', null, [
                'label' =>  'МКД'
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
     * @return \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private function getRouter()
    {
        return $this->getContainer()->get('router');
    }
}
