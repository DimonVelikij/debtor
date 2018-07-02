<?php

namespace AppBundle\Admin;

use AppBundle\EventGenerator\EventType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollection $collection
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
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
            ->add('alias', null, [
                'label' =>  'Алиас'
            ])
            ->add('type', null, [
                'label' =>  'Тип'
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
            ->add('alias', null, [
                'label' =>  'Алиас'
            ])
            ->add('typeTitle', null, [
                'label' =>  'Тип'
            ])
            ->add('_action', null, array(
                'label'     =>  'Действия',
                'actions'   =>  array(
                    'edit' => array()
                ),
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var array $templateFields */
        $templateFields = $this->getTemplateGenerator()->getTemplateFields();//поля подстановки для шаблонов

        $formMapper
            ->add('name', TextType::class, [
                'label'         =>  'Название',
                'required'      =>  true,
                'disabled'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' =>  'Укажите название шаблона'])
                ]
            ])
            ->add('alias', TextType::class, [
                'label'         =>  'Алиас',
                'required'      =>  true,
                'disabled'      =>  true
            ])
            ->add('type', ChoiceType::class, [
                'label'         =>  'Тип',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите тип'])
                ],
                'choices'       =>  EventType::$sonataTypeChoice,
                'disabled'      =>  true
            ])
            ->add('templateFields', ChoiceType::class, [
                'label'         =>  'Список полей для шаблона',
                'required'      =>  false,
                'choices'       =>  $templateFields,
                'multiple'      =>  true
            ])
            ->add('template', CKEditorType::class, [
                'label'         =>  'Шаблон',
                'required'      =>  false
            ]);
    }

    /**
     * @return null|\Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->getConfigurationPool()->getContainer();
    }

    /**
     * @return \AppBundle\Service\TemplateGenerator|object
     */
    private function getTemplateGenerator()
    {
        return $this->getContainer()->get('app.service.template_generator');
    }
}
