<?php

namespace AppBundle\Admin;

use AppBundle\Admin\traits\UserTrait;
use AppBundle\Entity\User;
use AppBundle\Validator\Constraints\HouseExist;
use AppBundle\Validator\Constraints\JudicialSector;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('managementStartDate', null ,[
                'label' =>  'Дата начала управления'
            ])
            ->add('managementEndDate', null, [
                'label' =>  'Дата окончания управления'
            ])
            ->add('legalDocumentName', null, [
                'label' =>  'Название документа на право управления'
            ])
            ->add('legalDocumentDate', null, [
                'label' =>  'Дата документа начала управления'
            ])
            ->add('legalDocumentNumber', null, [
                'label' =>  'Номер документа начала управления'
            ])
            ->add('judicialSector', null, [
                'label' =>  'Судебный участок'
            ])
            ->add('fsspDepartment', null, [
                'label' =>  'Отделение ФССП'
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
            ->add('managementStartDate', null ,[
                'label' =>  'Дата начала управления',
                'format'=>  'd.m.Y'
            ])
            ->add('managementEndDate', null, [
                'label' =>  'Дата окончания управления',
                'format'=>  'd.m.Y'
            ])
            ->add('legalDocumentName', null, [
                'label' =>  'Название документа на право управления'
            ])
            ->add('legalDocumentDate', null, [
                'label' =>  'Дата документа начала управления',
                'format'=>  'd.m.Y'
            ])
            ->add('legalDocumentNumber', null, [
                'label' =>  'Номер документа начала управления'
            ])
            ->add('judicialSector', null, [
                'label' =>  'Судебный участок'
            ])
            ->add('fsspDepartment', null, [
                'label' =>  'Отделение ФССП'
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
            ->tab('Дом и МКД')
                ->with('Дом', [
                    'class'     =>  'col-md-6',
                    'box_class' =>  'box box-solid box-success'
                ])
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
                ->end()
                ->with('МКД', [
                    'class'     =>  'col-md-6',
                    'box_class' =>  'box box-solid box-success'
                ])
                    ->add('managementStartDate', DateType::class, [
                        'label'         =>  'Дата начала управления',
                        'required'      =>  true,
                        'constraints'   =>  [
                            new NotBlank(['message' => 'Укажите дату начала управления'])
                        ],
                        'widget'        => 'single_text'
                    ])
                    ->add('managementEndDate', DateType::class, [
                        'label'         =>  'Дата окнчания управления',
                        'required'      =>  false,
                        'widget'        => 'single_text'
                    ])
                    ->add('legalDocumentName', TextType::class, [
                        'label'         =>  'Название документа на право управления',
                        'required'      =>  true,
                        'constraints'   =>  [
                            new NotBlank(['message' => 'Укажите название документа'])
                        ]
                    ])
                    ->add('legalDocumentDate', DateType::class, [
                        'label'         =>  'Дата документа начала управления',
                        'required'      =>  true,
                        'constraints'   =>  [
                            new NotBlank(['message' => 'Укажите дату документа'])
                        ],
                        'widget'        => 'single_text'
                    ])
                    ->add('legalDocumentNumber', TextType::class, [
                        'label'         =>  'Номер документа начала управления',
                        'required'      =>  false
                    ])
                    ->add('judicialSector', 'entity', [
                        'label'         =>  'Судебные участоки',
                        'required'      =>  true,
                        'class'         =>  'AppBundle\Entity\JudicialSector',
                        'help'          =>  "Необходимо привязать 'Районный', 'Мировой' и 'Арбитражный' суды<br><span style='color: blue;'>Если в списке нет нужных судебных участков, необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_judicialsector_create')}'>добавить судебные участоки</a> и обновить страницу</span>",
                        'constraints'   =>  [
                            new NotBlank(['message' => 'Укажите судебный участок']),
                            new JudicialSector(['types' =>  [
                                \AppBundle\Entity\JudicialSector::DISTRICT,
                                \AppBundle\Entity\JudicialSector::MAGISTRATE,
                                \AppBundle\Entity\JudicialSector::ARBITRATION
                            ]])
                        ],
                        'multiple'      =>  true
                    ])
                    ->add('fsspDepartment', 'entity', [
                        'label'         =>  'Отделение ФССП',
                        'required'      =>  true,
                        'class'         =>  'AppBundle\Entity\FSSPDepartment',
                        'help'          =>  "<span style='color: blue;'>Если в списке нет нужного отделения ФССП, необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_fsspdepartment_create')}'>добавить отделение ФССП</a> и обновить страницу</span>",
                        'constraints'   =>  [
                            new NotBlank(['message' => 'Укажите судебный участок'])
                        ]
                    ])
                ->end()
            ->end()
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
            ->add('managementStartDate', null ,[
                'label' =>  'Дата начала управления',
                'format'=>  'd.m.Y'
            ])
            ->add('managementEndDate', null, [
                'label' =>  'Дата окончания управления',
                'format'=>  'd.m.Y'
            ])
            ->add('legalDocumentName', null, [
                'label' =>  'Название документа на право управления'
            ])
            ->add('legalDocumentDate', null, [
                'label' =>  'Дата документа начала управления',
                'format'=>  'd.m.Y'
            ])
            ->add('legalDocumentNumber', null, [
                'label' =>  'Номер документа начала управления'
            ])
            ->add('judicialSector', null, [
                'label' =>  'Судебный участок'
            ])
            ->add('fsspDepartment', null, [
                'label' =>  'Отделение ФССП'
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
