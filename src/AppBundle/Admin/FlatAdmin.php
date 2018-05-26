<?php

namespace AppBundle\Admin;

use AppBundle\Admin\traits\UserTrait;
use AppBundle\Entity\Flat;
use AppBundle\Entity\User;
use AppBundle\Service\AddressBookValidator;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
                ->andWhere($query->expr()->eq('house.company', $user->getCompany()->getId()));
        }

        return $query;
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
            ->add('subscriber_list', 'subscriber_list/{flat_id}')
            ->add('submit_subscriber', 'submit_subscriber')
            ->add('personal_accounts', 'personal_accounts/{flat_id}')
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
            ->add('subscribers.personalAccount.account', null, [
                'label' =>  'Лицевой счет'
            ])
            ->add('house.street.city', null, [
                'label' =>  'Город'
            ])
            ->add('house.street', null, [
                'label' =>  'Улица'
            ])
            ->add('house', null, [
                'label' =>  'Дом'
            ])
            ->add('number', null, [
                'label' =>  'Номер помещения'
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
            ->add('house.street.city', null, [
                'label'     =>  'Город',
                'template'  =>  '@App/Admin/Flat/List/city.html.twig'
            ])
            ->add('house.street', null, [
                'label'     =>  'Улица',
                'template'  =>  '@App/Admin/Flat/List/street.html.twig'
            ])
            ->add('house', null, [
                'label'     =>  'Дом',
                'template'  =>  '@App/Admin/Flat/List/house.html.twig'
            ])
            ->add('number', null, [
                'label'     =>  'Номер помещения',
                'sortable'  =>  false
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

        $formMapper
            ->tab('Помещение')
                ->with('Адрес', [
                    'class'     =>  'col-md-3',
                    'box_class' =>  'box box-solid box-success'
                ])
                    ->add('house', 'entity', [
                        'label'         =>  'Дом',
                        'class'         =>  'AppBundle\Entity\House',
                        'required'      =>  true,
                        'group_by'      =>  'street.city',
                        'help'          =>  "<span style='color: blue;'>Если в списке нет нужного дома, необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_house_create')}'>добавить дом</a> и обновить страницу</span>",
                        'constraints'   =>  [
                            new NotBlank(['message' => 'Укажите дом'])
                        ],
                        'query_builder' =>  function (EntityRepository $er) use ($user) {
                            if (!$user->isSuperAdmin()) {
                                //выводим дома, принадлежащие только УК пользователя
                                $er = $er->createQueryBuilder('house');

                                return $er
                                    ->andWhere(
                                        $er->expr()->eq('house.company', $user->getCompany()->getId())
                                    );
                            }
                        }
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
                ->with('Период взыскания', [
                    'class'     =>  'col-md-3',
                    'box_class' =>  'box box-solid box-success'
                ])
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
                ->with('Основной долг', [
                    'class'     =>  'col-md-3',
                    'box_class' =>  'box box-solid box-success'
                ])
                    ->add('dateFillDebt', DateType::class, [
                        'label'         =>  'Дата заполнения долга',
                        'required'      =>  false,
                        'widget'        =>  'single_text',
                        'help'          =>  'Оставьте поле пустым, подставится текущая дата'
                    ])
                    ->add('sumDebt', TextType::class, [
                        'label'         =>  'Сумма долга, руб.',
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
                ->with('Пени', [
                    'class'     =>  'col-md-3',
                    'box_class' =>  'box box-solid box-success'
                ])
                    ->add('dateFillFine', DateType::class, [
                        'label'     =>  'Дата заполнения пени',
                        'required'  =>  false,
                        'widget'    =>  'single_text',
                        'help'      =>  'Оставьте поле пустым, подставится текущая дата'
                    ])
                    ->add('sumFine', TextType::class, [
                        'label'     =>  'Сумма пени, руб.',
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
            ->end();

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
     * @return \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private function getRouter()
    {
        return $this->getContainer()->get('router');
    }
}
