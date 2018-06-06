<?php

namespace AppBundle\Admin;

use AppBundle\Admin\traits\UserTrait;
use AppBundle\Entity\Event;
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
            ->add('logs', 'logs/{flat_id}')
            ->add('read_logs', 'read_logs/{flat_id}')
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
            ->add('isGenerateErrors', null, [
                'label' =>  'Ошибки генерации шаблона'
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
            ->add('isGenerateErrors', null, [
                'label'     =>  'Ошибки генерации шаблона'
            ])
            ->add('isNewLogs', 'boolean', [
                'label'     =>  'Новые события',
                'sortable'  =>  true
            ])
            ->add('updatedAt', null, [
                'label'     =>  'Дата последнего обновления',
                'template'  =>  '@App/Admin/Flat/List/updated_at.html.twig'
            ])
            ->add('subscribers.personalAccount.account', null, [
                'label'     =>  'Лицевые счета',
                'template'  =>  '@App/Admin/Flat/List/personal_account.html.twig'
            ])
            ->add('event', null, [
                'label'     =>  'Текущее событие',
                'template'  =>  '@App/Admin/Flat/List/current_action.html.twig'
            ])
            ->add('event.parent', null, [
                'label'     =>  'Следующее событие'
            ])
            ->add('_action', null, array(
                'label'     =>  'Действия',
                'actions'   => array(
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

        /** @var Event $startEvent */
        $startEvent = $this->getDoctrine()->getRepository('AppBundle:Event')
            ->createQueryBuilder('event')
            ->where('event.isStart = :isStart')
            ->setParameter('isStart', true)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $formMapper->getFormBuilder()->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($addressBookValidator, $startEvent) {
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

            if (!$startEvent) {
                $event->getForm()->get('number')->addError(new FormError('Прежде чем добавить помещение - нужно добавить событие'));
            }
        });

        $formMapper->getFormBuilder()->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($startEvent) {
            /** @var Flat $flat */
            $flat = $event->getData();

            //если новый объект и форма валидна
            if (!$flat->getId() && $event->getForm()->isValid()) {
                $flat
                    ->addEvent($startEvent)//стартовый шаблон
                    ->setEventData([
                        'lastDataGenerate' => new \DateTime()//дата последней генерации шаблона - текущая
                    ])
                ;
            }

            //если были ошибки генерации шаблона, при сохранени записи считаем что они исправлены
            $flat->setIsGenerateErrors(false);
        });
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

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getDoctrine()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
