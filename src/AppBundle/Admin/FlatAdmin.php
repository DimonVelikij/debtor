<?php

namespace AppBundle\Admin;

use AppBundle\Admin\traits\UserTrait;
use AppBundle\Entity\Flat;
use AppBundle\Entity\House;
use AppBundle\Entity\User;
use AppBundle\Service\AddressBookValidator;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

class FlatAdmin extends AbstractAdmin
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
                ->innerJoin('o.house', 'house')
                ->andWhere(
                    $query->expr()->eq('house.company', $user->getCompany()->getId())
                );
        }

        return $query;
    }

    /**
     * @param $name
     * @return mixed|null|string
     */
    public function getTemplate($name)
    {
         $this->templates['edit'] = 'AppBundle:Admin\Flat:edit.html.twig';

         return parent::getTemplate($name);
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('debtor_list', 'debtor_list/{flat_id}')
            ->add('debtor_types', 'debtor_types')
            ->add('ownership_statuses', 'ownership_statuses')
            ->add('submit_debtor', 'submit_debtor')
            ->remove('batch')
            ->remove('export')
            ->remove('delete');
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('number', null, [
                'label' =>  'Номер помещения'
            ])
            ->add('house', null, [
                'label' =>  'Адрес'
            ])
            ->add('archive', null, [
                'label' =>  'Архивный'
            ])
            ->add('updatedAt', 'doctrine_orm_date_range', [
                'label'         =>  'Дата последнего обновления',
                'field_type'    =>  'sonata_type_date_range_picker'
            ], 'sonata_type_date_range_picker', [
                'field_options_start' => [
                    'format'    => 'dd.MM.yyyy',
                    'label'     => 'начиная с:'
                ],
                'field_options_end' => [
                    'format'    => 'dd.MM.yyyy',
                    'label'     => 'по:'
                ]
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('house', null, [
                'label'     =>  'Адрес'
            ])
            ->add('number', null, [
                'label'     =>  'Номер помещения'
            ])
            ->add('sumDebt', null, [
                'label'     =>  'Сумма долга, руб.'
            ])
            ->add('sumFine', null, [
                'label'     =>  'Сумма пени, руб.'
            ])
            ->add('archive', null, [
                'label'     =>  'Архивный'
            ])
            ->add('updatedAt', null, [
                'label'     =>  'Дата последнего обновления',
                'template'  =>  '@App/Admin/Flat/List/updated_at.html.twig'
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

        $houseQueryBuilder = $this->getEntityManager()->getRepository('AppBundle:House')
            ->createQueryBuilder('house');

        if (!$user->isSuperAdmin()) {
            $houseQueryBuilder
                ->andWhere(
                    $houseQueryBuilder->expr()->eq('house.company', $user->getCompany()->getId())
                );
        }

        $houses = $houseQueryBuilder
            ->innerJoin('house.street', 'street')
            ->orderBy('street.city')
            ->getQuery()
            ->getResult();

        $houseHelp = count($houses) ?
            "<span style='color: blue;'>Если в списке нет нужного дома, необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_house_create')}'>добавить дом</a> и обновить страницу</span>" :
            "<span style='color: red'>Список домов пуст. Необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_house_create')}'>добавить дом</a> и обновить страницу</span>";

        $houseChoice = [];

        /** @var House $house */
        foreach ($houses as $house) {
            if (!isset($houseChoice[$house->getStreet()->getCity()->getTitle() . ', ' . $house->getStreet()->getTitle()])) {
                $houseChoice[$house->getStreet()->getCity()->getTitle() . ', ' . $house->getStreet()->getTitle()] = [];
            }
            $houseChoice[$house->getStreet()->getCity()->getTitle() . ', ' . $house->getStreet()->getTitle()][$house->getStreet()->getCity()->getTitle() . ', ' . $house->getStreet()->getTitle() . ', ' . $house->getNumber()] = $house;
        }

        $formMapper
            ->with('Помещение')
                ->add('house', ChoiceType::class, [
                    'label'         =>  'Дом',
                    'choices'       =>  $houseChoice,
                    'required'      =>  true,
                    'help'          =>  $houseHelp,
                    'constraints'   =>  [
                        new NotBlank(['message' => 'Укажите дом'])
                    ]
                ])
                ->add('number', TextType::class, [
                    'label'         =>  'Номер помещения',
                    'required'      =>  true,
                    'constraints'   =>  [
                        new NotBlank(['message' => 'Укажите номер помещения'])
                    ]
                ])
                ->add('archive', CheckboxType::class, [
                    'label'         =>  'Больше не является должником (Отправить в архив)',
                    'required'      =>  false
                ])
            ->end()
            ->with('Основной долг', ['class' => 'col-md-4'])
                ->add('dateFillDebt', DateType::class, [
                    'label'         =>  'Дата заполнения основного долга',
                    'required'      =>  false,
                    'widget'        =>  'single_text',
                    'help'          =>  'Оставьте поле пустым, подставится текущая дата'
                ])
                ->add('sumDebt', TextType::class, [
                    'label'         =>  'Сумма долга',
                    'required'      =>  true,
                    'constraints'   =>  [
                        new NotBlank(['message' => 'Укажите сумма долга'])
                    ]
                ])
                ->add('periodAccruedDebt', TextType::class, [
                    'label'         =>  'За период начислено',
                    'required'      =>  false
                ])
                ->add('periodPayDebt', TextType::class, [
                    'label'         =>  'За период оплачено',
                    'required'      =>  false
                ])
            ->end()
            ->with('Пени', ['class' => 'col-md-4'])
                ->add('dateFillFine', DateType::class, [
                    'label'     =>  'Дата заполнения пени',
                    'required'  =>  false,
                    'widget'    =>  'single_text',
                    'help'      =>  'Оставьте поле пустым, подставится текущая дата'
                ])
                ->add('sumFine', TextType::class, [
                    'label'     =>  'Сумма долга',
                    'required'  =>  false
                ])
                ->add('periodAccruedFine', TextType::class, [
                    'label'         =>  'За период начислено',
                    'required'      =>  false
                ])
                ->add('periodPayFine', TextType::class, [
                    'label'         =>  'За период оплачено',
                    'required'      =>  false
                ])
            ->end()
            ->with('Период взыскания', ['class' => 'col-md-4'])
                ->add('startDebtPeriod', DateType::class, [
                    'label'     =>  'Начало периода взыскания',
                    'required'  =>  false,
                    'widget'    => 'single_text'
                ])
                ->add('endDebtPeriod', DateType::class, [
                    'label'     =>  'Конец периода взыскания',
                    'required'  =>  false,
                    'widget'    => 'single_text'
                ])
            ->end()
        ;

        /** @var AddressBookValidator $addressBookValidator */
        $addressBookValidator = $this->getContainer()->get('app.service.address_book_validator');
        $formMapper->getFormBuilder()->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($addressBookValidator) {
            /** @var Flat $flat */
            $flat = $event->getData();

            if (!$flat->getDateFillDebt()) {
                $flat->setDateFillDebt(new \DateTime());
            }

            if (!$flat->getDateFillFine()) {
                $flat->setDateFillFine(new \DateTime());
            }

            $error = $addressBookValidator->validateFlat($flat);
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
        $showMapper
            ->add('id')
            ->add('number', null, [
                'label'     =>  'Номер помещения'
            ])
            ->add('startDebtPeriod', null, [
                'label'     =>  'Начало периода взыскания'
            ])
            ->add('endDebtPeriod', null, [
                'label'     =>  'Конец периода взыскания'
            ])
            ->add('dateFillDebt', null, [
                'label'     =>  'Дата заполнения долга',
                'template'  =>  '@App/Admin/Flat/Show/date_fill_debt.html.twig'
            ])
            ->add('sumDebt', null, [
                'label'     =>  'Сумма долга'
            ])
            ->add('periodAccruedDebt', null, [
                'label'     =>  'За период начислено долга'
            ])
            ->add('periodPayDebt', null, [
                'label'     =>  'За период оплачено долга'
            ])
            ->add('dateFillFine', null, [
                'label'     =>  'Дата заполнения пени',
                'template'  =>  '@App/Admin/Flat/Show/date_fill_fine.html.twig'
            ])
            ->add('sumFine', null, [
                'label'     =>  'Сумма пени'
            ])
            ->add('periodAccruedFine', null, [
                'label'     =>  'За период начислено пени'
            ])
            ->add('periodPayFine', null, [
                'label'     =>  'За период оплачено пени'
            ])
            ->add('archive', null, [
                'label'     =>  'Архивный'
            ])
            ->add('updatedAt', null, [
                'label'     =>  'Дата последнего обновления',
                'template'  =>  '@App/Admin/Flat/Show/updated_at.html.twig'
            ])
            ->add('house', null, [
                'label'     =>  'Адрес'
            ])
            ->add('debtors', null, [
                'label'     =>  'Список должников',
                'template'  =>  '@App/Admin/Flat/Show/debtors.html.twig'
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
