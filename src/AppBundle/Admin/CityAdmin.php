<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class CityAdmin extends AbstractAdmin
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
            ->add('id')
            ->add('title', null, [
                'label' =>  'Название'
            ])
            ->add('slug', null, [
                'label' =>  'Алиас'
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
            ->add('slug', null, [
                'label' =>  'Алиас'
            ])
            ->add('_action', null, array(
                'label'     =>  'Действия',
                'actions'       =>  array(
                    'show'      => array(),
                    'edit'      => array(),
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
                'label'         =>  'Название',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите название города'])
                ]
            ])
            ->add('slug', TextType::class, [
                'label'         =>  'Алиас',
                'required'      =>  false,
                'sonata_help'   =>  'Сгенерируется автоматически по полю "Название"',
                'disabled'      =>  true
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
            ->add('slug', null, [
                'label' =>  'Алиас'
            ])
        ;
    }
}
