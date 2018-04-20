<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Street;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class HouseAdmin extends AbstractAdmin
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
            ->add('number', null, [
                'label' =>  'Номер дома'
            ])
            ->add('street', null, [
                'label' =>  'Улица'
            ])
            ->add('street.city', null, [
                'label' =>  'Город'
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
            ->add('number', null, [
                'label' =>  'Номер дома'
            ])
            ->add('street', null, [
                'label' =>  'Улица'
            ])
            ->add('street.city', null, [
                'label' =>  'Город'
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
        $streets = $this->getEntityManager()
            ->getRepository('AppBundle:Street')
            ->createQueryBuilder('s')
            ->orderBy('s.city')
            ->getQuery()
            ->getResult();

        $streetHelp = count($streets) ?
            "<span style='color: blue;'>Если в списке нет нужной улицы, необнодимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_street_create')}'>добавить улицу</a> и обновить страницу</span>" :
            "<span style='color: red'>Список улиц пуст. Необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_street_create')}'>добавить улицу</a> и обновить страницу</span>";

        $streetChoice = [];

        /** @var Street $street */
        foreach ($streets as $street) {
            $streetChoice[$street->getCity()->getTitle() . ', ' . $street->getTitle()] = $street;
        }

        $formMapper
            ->add('street', ChoiceType::class, [
                'label'     =>  'Город, улица',
                'choices'   =>  $streetChoice,
                'group_by'  =>  function ($value, $key, $index) {
                    return $value->getCity()->getTitle();
                },
                'help'      =>  $streetHelp
            ])
            ->add('number', TextType::class, [
                'label' =>  'Номер дома'
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
            ->add('number', null, [
                'label' =>  'Номер дома'
            ])
            ->add('street', null, [
                'label' =>  'Улица'
            ])
            ->add('street.city', null, [
                'label' =>  'Город'
            ])
        ;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private function getRouter()
    {
        return $this->getConfigurationPool()->getContainer()->get('router');
    }
}
