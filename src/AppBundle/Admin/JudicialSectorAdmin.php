<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Validator\Constraints\NotBlank;

class JudicialSectorAdmin extends AbstractAdmin
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
            ->add('name', null, [
                'label' =>  'Название'
            ])
            ->add('address', null, [
                'label' =>  'Адрес'
            ])
            ->add('requisites', null, [
                'label' =>  'Реквизиты'
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
            ->add('name', null, [
                'label' =>  'Название'
            ])
            ->add('address', null, [
                'label' =>  'Адрес'
            ])
            ->add('requisites', null, [
                'label' =>  'Реквизиты'
            ])
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
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
            ->add('name', null, [
                'label'         =>  'Название',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите название'])
                ]
            ])
            ->add('address', null, [
                'label'         =>  'Адрес',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите адрес'])
                ]
            ])
            ->add('requisites', null, [
                'label'         =>  'Реквизиты',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите реквизиты'])
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
            ->add('name', null, [
                'label' =>  'Название'
            ])
            ->add('address', null, [
                'label' =>  'Адрес'
            ])
            ->add('requisites', null, [
                'label' =>  'Реквизиты'
            ])
        ;
    }
}
