<?php

namespace AppBundle\Admin;

use AppBundle\Admin\traits\UserTrait;
use AppBundle\Entity\Event;
use AppBundle\Entity\Flat;
use AppBundle\Entity\FlatEvent;
use AppBundle\Entity\User;
use AppBundle\EventGenerator\Generator\GeneratorAggregate;
use AppBundle\Service\FlatLogger;
use AppBundle\Validator\Constraints\FlatExist;
use Doctrine\ORM\EntityManager;
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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

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
            ->add('process_user', 'process_user/{event}')
            ->add('perform', 'perform/{event}')
            ->add('miss', 'miss/{event}')
            ->add('finish', 'finish/{flat_id}')
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
                'label' =>  'Квартира'
            ])
            ->add('subscribers.personalAccount.account', null, [
                'label' =>  'л/с'
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
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
                'label'     =>  'Квартира',
                'sortable'  =>  false
            ])
            ->add('subscribers.personalAccount.account', null, [
                'label'     =>  'л/с',
                'template'  =>  '@App/Admin/Flat/List/personal_account.html.twig'
            ])
            ->add('dateFillDebt', null, [
                'label'     =>  'Дата',
                'format'    =>  'd.m.Y'
            ])
            ->add('sumDebt', null, [
                'label'     =>  'Долг, руб.'
            ])
            ->add('sumFine', null, [
                'label'     =>  'Пени, руб.'
            ])
            ->add('isGenerateErrors', null, [
                'label'     =>  'Примечание',
                'template'  =>  '@App/Admin/Flat/List/is_generate_errors.html.twig',
                'sortable'  =>  false
            ])
            ->add('event', null, [
                'label'     =>  'Текущее событие',
                'template'  =>  '@App/Admin/Flat/List/current_event.html.twig'
            ])
            ->add('event.parent', null, [
                'label'     =>  'Следующее событие',
                'template'  =>  '@App/Admin/Flat/List/next_event.html.twig'
            ])
            ->add('_action', null, array(
                'label'     =>  'Действия',
                'actions'   => array(
                    'edit'  => array(),
                ),
                'template'  =>  '@App/Admin/Flat/List/list__action_edit.html.twig'
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

        /** @var Flat $flat */
        $flat = $this->getSubject();

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
                        'help'          =>  $flat->getId() ? "" : "<span style='color: blue;'>Если в списке нет нужного дома, необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_house_create')}'>добавить дом</a> и обновить страницу</span>",
                        'constraints'   =>  [
                            new NotBlank(['message' => 'Укажите дом']),
                            new FlatExist()
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
                    ->add('type', null, [
                        'label'         =>  'Тип',
                        'class'         =>  'AppBundle\Entity\FlatType',
                        'required'      =>  true,
                        'help'          =>  $flat->getId() ? "" : "<span style='color: blue;'>Если в списке нет нужного типа квартиры, необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_flattype_create')}'>добавить тип квартиры</a> и обновить страницу</span>",
                        'constraints'   =>  [
                            new NotBlank(['message' => 'Укажите тип помещения'])
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
                            new NotBlank(['message' => 'Укажите сумма долга']),
                            new Regex(['pattern' => '/^(\d+)(\.\d{1,2})?$/', 'message' => 'Неверно указана сумма долга'])
                        ],
                        'help'          =>  'Укажите копейки через точку'
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
                        'label'         =>  'Сумма пени, руб.',
                        'required'      =>  false,
                        'constraints'   =>  [
                            new Regex(['pattern' => '/^(\d+)(\.\d{1,2})?$/', 'message' => 'Неверно указана сумма пени'])
                        ],
                        'help'          =>  'Укажите копейки через точку'
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

        $formMapper->getFormBuilder()->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Flat $flat */
            $flat = $event->getData();

            if (!$flat->getDateFillDebt()) {
                $flat->setDateFillDebt(new \DateTime());
            }

            if (!$flat->getDateFillFine()) {
                $flat->setDateFillFine(new \DateTime());
            }
        });

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.doctrine_entity_manager');

        /** @var FlatLogger $flatLogger */
        $flatLogger = $this->getContainer()->get('app.service.flat_logger');

        $formMapper->getFormBuilder()->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($em, $flatLogger) {
            /** @var Flat $flat */
            $flat = $event->getData();

            //если были ошибки генерации шаблона, при сохранени записи считаем что они исправлены
            $flat->setIsGenerateErrors(false);

            if (
                !$flat->getId() ||
                ($flat->getId() && !$flat->getFlatsEvents()->count() && $flat->getSumDebt() + $flat->getSumFine() >= GeneratorAggregate::TOTAL_DEBT)
            ) {//если помещение новое или старое помещение у которого нет событий и сумма долга и пени больше либо равно 5000
                /** @var Event $event */
                $event = $em->getRepository('AppBundle:Event')->findOneBy(['alias' => 'entered_processing']);
                //создаем событие "поступил в работу"
                $flatEvent = new FlatEvent();
                $flatEvent
                    ->setFlat($flat)
                    ->setEvent($event)
                    ->setDateGenerate(new \DateTime())
                    ->setData(['show' => '']);

                $flat->addFlatsEvent($flatEvent);

                //пишем лог
                //если добавлять через метод log - будет ошибка вставки лога, т.к. помещения еще не существует
                $flat->addLog($flatLogger->createLog($flat, "<b>{$event->getName()}</b>", $event));
            }
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
}
