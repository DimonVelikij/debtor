<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Company;
use AppBundle\Entity\User;
use AppBundle\Validator\Constraints\OldPassword;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserAdmin extends AbstractAdmin
{
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
        $datagridMapper
            ->add('fullName', null, [
                'label' =>  'ФИО'
            ])
            ->add('username', null, [
                'label' =>  'Логин'
            ])
            ->add('email', null, [
                'label' =>  'Email'
            ])
            ->add('enabled', null, [
                'label' =>  'Учетная запись включена'
            ])
            ->add('company', null, [
                'label' =>  'Управляющая компания'
            ])
            ->add('lastLogin', 'doctrine_orm_date_range', [
                'label'         =>  'Последняя авторизация',
                'field_type'    => 'sonata_type_date_range_picker'
            ], 'sonata_type_date_range_picker', [
                'field_options_start' => [
                    'format' => 'dd.MM.yyyy',
                    'label' => 'начиная с:'
                ],
                'field_options_end' => [
                    'format' => 'dd.MM.yyyy',
                    'label' => 'по:'
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
            ->add('fullName', null, [
                'label'     =>  'ФИО'
            ])
            ->add('username',null, [
                'label'     =>  'Логин'
            ])
            ->add('email', null, [
                'label'     =>  'Email'
            ])
            ->add('enabled', null, [
                'label'     =>  'Учетная запись включена'
            ])
            ->add('company', EntityType::class, [
                'label'     =>  'Управляющая компания'
            ])
            ->add('lastLogin', null, [
                'label'     =>  'Последняя авторизация',
                'template'  =>  '@App/Admin/User/List/last_login.html.twig'
            ])
            ->add('_action', null, array(
                'label'     =>  'Действия',
                'actions'   =>  array(
                    'show'  =>  array(),
                    'edit'  =>  array(),
                ),
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $countCompanies = (bool)$this->getDoctrine()
            ->getRepository('AppBundle:Company')
            ->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        /** @var User $user */
        $user = $this->getSubject();

        $formMapper
            ->add('fullName', TextType::class, [
                'label'         =>  'ФИО',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message' => 'Укажите ФИО'
                    ])
                ]
            ])
            ->add('username', TextType::class, [
                'label'         =>  'Логин',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message'   =>  'Укажите Логин'
                    ])
                ]
            ])
            ->add('email', TextType::class, [
                'label'         =>  'Email',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message'   =>  'Укажите Email'
                    ]),
                    new Email([
                        'message'   =>  'Неверно указан Email'
                    ])
                ]
            ]);

        if (!$user->isSuperAdmin()) {
            $formMapper
                ->add('enabled', CheckboxType::class, [
                    'label' => 'Включить учетную запись',
                    'required' => false
                ])
                ->add('company', EntityType::class, [
                    'label'         =>  'Управляющая компания',
                    'required'      =>  true,
                    'choice_label'  =>  'title',
                    'class'         =>  Company::class,
                    'help'          =>  $countCompanies || $user->getCompany() ?
                        "" :
                        "<span style='color: red'>Для добавления пользователя необходимо <a target='_blank' href='{$this->getContainer()->get('router')->generate('admin_app_company_create')}'>добавить управляющую компанию</a></span>",
                    'constraints'   =>  [
                        new NotBlank([
                            'message' => 'Выберите управлющую компанию'
                        ])
                    ]
                ]);
        }

        if (!$user->getId()) {
            $formMapper
                ->add('password', PasswordType::class, [
                    'label'         =>  'Пароль',
                    'required'      =>  true,
                    'help'          =>  'Длина пароля должна быть от 8 до 16 символов',
                    'constraints'   =>  [
                        new NotBlank([
                            'message'   =>  'Укажите пароль'
                        ]),
                        new Length([
                            'min'           =>  8,
                            'minMessage'    =>  'Пароль должен содержать не менее 8 символов',
                            'max'           =>  16,
                            'maxMessage'    =>  'Пароль должен содержать не более 16 символов'
                        ])
                    ]
                ]);
        } else {
            $formMapper
                ->add('oldPassword', PasswordType::class, [
                    'label'         =>  'Старый пароль',
                    'required'      =>  false,
                    'constraints'   =>  [
                        new OldPassword([
                            'user'  =>  $user
                        ])
                    ]
                ])
                ->add('newPassword', RepeatedType::class, [
                    'type'              =>  PasswordType::class,
                    'invalid_message'   =>  'Пароли должны совпадать',
                    'required'          =>  false,
                    'first_options'     =>  [
                        'label'         =>  'Новый пароль'
                    ],
                    'second_options'    =>  [
                        'label' =>  'Повторите пароль'
                    ],
                    'constraints'       =>  [
                        new Length([
                            'min'           =>  8,
                            'minMessage'    =>  'Пароль должен содержать не менее 8 символов',
                            'max'           =>  16,
                            'maxMessage'    =>  'Пароль должен содержать не более 16 символов'
                        ])
                    ]
                ]);

            $formMapper->getFormBuilder()->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                /** @var Form $form */
                $form = $event->getForm();

                /** @var User $user */
                $user = $event->getData();

                //если пользователь ввел старый пароль, а новый не ввел - показываем ошибку
                if ($user->getOldPassword() && !$user->getNewPassword()) {
                    $form->get('newPassword')['first']->addError(new FormError('Укажите новый пароль'));
                }
            });
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        /** @var User $user */
        $user = $this->getSubject();

        $showMapper
            ->add('id')
            ->add('fullName', null, [
                'label' =>  'ФИО'
            ])
            ->add('username', null, [
                'label' =>  'Логин'
            ])
            ->add('email', null, [
                'label' =>  'Email'
            ])
            ->add('enabled', null, [
                'label' =>  'Учетная запись влючена'
            ]);

        if (!$user->isSuperAdmin()) {
            $showMapper
                ->add('company', null, [
                    'label' =>  'Управляющая компания'
                ]);
        }

        $showMapper
            ->add('lastLogin', null, [
                'label'     =>  'Последняя авторизация',
                'template'  =>  '@App/Admin/User/Show/last_login.html.twig'
            ])
            ->add('userRole', null, [
                'label' =>  'Роль'
            ])
        ;
    }

    /**
     * @param User $user
     * @return User
     */
    public function create($user)
    {
        /** @var UserPasswordEncoder $userPasswordEncoder */
        $userPasswordEncoder = $this->getContainer()->get('security.password_encoder');

        $user
            ->setSalt(sha1(md5(mt_rand())))
            ->setPassword($userPasswordEncoder->encodePassword($user, $user->getPassword()))
            ->setRoles(['ROLE_ADMIN']);

        $user = parent::create($user);

        return $user;
    }

    /**
     * вызов при обновлении записи
     * @param User $user
     */
    public function preUpdate($user)
    {
        if ($user->getNewPassword()) {
            /** @var UserPasswordEncoder $userPasswordEncoder */
            $userPasswordEncoder = $this->getContainer()->get('security.password_encoder');

            $user->setPassword($userPasswordEncoder->encodePassword($user, $user->getNewPassword()));
        }
    }

    /**
     * @return null|\Symfony\Component\DependencyInjection\ContainerInterface
     */
    private function getContainer()
    {
        return $this->getConfigurationPool()->getContainer();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getDoctrine()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
