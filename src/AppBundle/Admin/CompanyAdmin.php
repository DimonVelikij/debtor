<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompanyAdmin extends AbstractAdmin
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
            ->add('title', null, [
                'label' =>  'Название'
            ])
            ->add('ogrn', null, [
                'label' =>  'ОГРН'
            ])
            ->add('inn', null, [
                'label' =>  'ИНН'
            ])
            ->add('address', null, [
                'label' =>  'Адрес'
            ])
            ->add('postAddress', null, [
                'label' =>  'Почтовый адрес'
            ])
            ->add('phone', null, [
                'label' =>  'Телефон'
            ])
            ->add('email', null, [
                'label' =>  'Email'
            ])
            ->add('checkingAccount', null, [
                'label' =>  'Расчетный счет'
            ])
            ->add('bankName', null, [
                'label' =>  'Наименование банка'
            ])
            ->add('bik', null, [
                'label' =>  'БИК'
            ])
            ->add('correspondentAccount', null, [
                'label' =>  'Корреспондентский счет'
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
            ->add('title', null, [
                'label' =>  'Название'
            ])
            ->add('ogrn', null, [
                'label' =>  'ОГРН'
            ])
            ->add('inn', null, [
                'label' =>  'ИНН'
            ])
            ->add('address', null, [
                'label' =>  'Адрес'
            ])
            ->add('postAddress', null, [
                'label' =>  'Почтовый адрес'
            ])
            ->add('phone', null, [
                'label' =>  'Телефон'
            ])
            ->add('email', null, [
                'label' =>  'Email'
            ])
            ->add('checkingAccount', null, [
                'label' =>  'Расчетный счет'
            ])
            ->add('bankName', null, [
                'label' =>  'Наименование банка'
            ])
            ->add('bik', null, [
                'label' =>  'БИК'
            ])
            ->add('correspondentAccount', null, [
                'label' =>  'Корреспондентский счет'
            ])
            ->add('_action', null, array(
                'label'     =>  'Действия',
                'actions'   => array(
                    'show'  => array(),
                    'edit'  => array()
                ),
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', TextType::class, [
                'label'     =>  'Название',
                'required'  =>  true
            ])
            ->add('ogrn', TextType::class, [
                'label'         =>  'ОГРН',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message' => 'Введите ОГРН'
                    ])
                ]
            ])
            ->add('inn', TextType::class, [
                'label'         =>  'ИНН',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message' => 'Введите ИНН'
                    ])
                ]
            ])
            ->add('address', TextType::class, [
                'label'         =>  'Адрес',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message' => 'Введите адрес'
                    ])
                ]
            ])
            ->add('postAddress', TextType::class, [
                'label'         =>  'Почтовый адрес',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message' => 'Введите почтовый адрес'
                    ])
                ]
            ])
            ->add('phone', TextType::class, [
                'label'         =>  'Телефон',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message' => 'Введите телефон'
                    ])
                ]
            ])
            ->add('email', TextType::class, [
                'label'         =>  'Email',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message' => 'Введите Email'
                    ]),
                    new Email([
                        'message' => 'Неверно введен Email'
                    ])
                ]
            ])
            ->add('checkingAccount', TextType::class, [
                'label'         =>  'Расчетный счет',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message'   =>  'Введите расчетный счет'
                    ])
                ]
            ])
            ->add('bankName', TextType::class, [
                'label'         =>  'Наименование банка',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message'   =>  'Введите наименование банка'
                    ])
                ]
            ])
            ->add('bik', TextType::class, [
                'label'         =>  'БИК',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message'   =>  'Введите БИК'
                    ])
                ]
            ])
            ->add('correspondentAccount', TextType::class, [
                'label'         =>  'Корреспондентский счет',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank([
                        'message'   =>  'Введите корреспондентский счет'
                    ])
                ]
            ])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('title', null, [
                'label' =>  'Название'
            ])
            ->add('ogrn', null, [
                'label' =>  'ОГРН'
            ])
            ->add('inn', null, [
                'label' =>  'ИНН'
            ])
            ->add('address', null, [
                'label' =>  'Адрес'
            ])
            ->add('postAddress', null, [
                'label' =>  'Почтовый адрес'
            ])
            ->add('phone', null, [
                'label' =>  'Телефон'
            ])
            ->add('email', null, [
                'label' =>  'Email'
            ])
            ->add('users', null, [
                'label' =>  'Пользователи'
            ])
            ->add('checkingAccount', null, [
                'label' =>  'Расчетный счет'
            ])
            ->add('bankName', null, [
                'label' =>  'Наименование банка'
            ])
            ->add('bik', null, [
                'label' =>  'БИК'
            ])
            ->add('correspondentAccount', null, [
                'label' =>  'Корреспондентский счет'
            ])
        ;
    }
}
