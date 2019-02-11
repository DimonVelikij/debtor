<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Street;
use AppBundle\Validator\Constraints\StreetExist;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class StreetAdmin extends AbstractAdmin
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
            ->add('slug', null, [
                'label' =>  'Алиас'
            ])
            ->add('city', null, [
                'label' =>  'Город'
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
            ->add('title', null, [
                'label' =>  'Название'
            ])
            ->add('slug', null, [
                'label' =>  'Алиас'
            ])
            ->add('city', null, [
                'label' =>  'Город'
            ])
            ->add('type', null, [
                'label' =>  'Тип'
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
        /** @var Street $street */
        $street = $this->getSubject();

        $formMapper
            ->add('city', 'entity', [
                'label'         =>  'Город',
                'class'         =>  'AppBundle\Entity\City',
                'required'      =>  true,
                'help'          =>  "<span style='color: blue;'>Если в списке нет нужного города, необнодимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_city_create')}'>добавить город</a> и обновить страницу</span>",
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите город'])
                ]
            ])
            ->add('type', 'entity', [
                'label'         =>  'Тип',
                'class'         =>  'AppBundle\Entity\StreetType',
                'required'      =>  true,
                'help'          =>  "<span style='color: blue;'>Если в списке нет нужного типа улицы, необнодимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_streettype_create')}'>добавить вид улицы</a> и обновить страницу</span>",
                'constraints'   =>  [
                    new NotBlank(['message' =>  'Укажите тип улицы'])
                ]
            ])
            ->add('title', TextType::class, [
                'label'         =>  'Название',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите название улицы']),
                    new StreetExist(['streetId' => $street->getId()])
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
            ->add('city', null, [
                'label' =>  'Город'
            ])
            ->add('type', null, [
                'label' =>  'Тип'
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
