<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DebtorAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('types', 'types')
            ->add('companies', 'companies')
            ->add('ownership_statuses', 'ownership_statuses')
            ->add('save', 'save');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, [
                'label' =>  'ФИО/Наименование'
            ])
            ->add('debtorType', null, [
                'label' =>  'Тип должника',
                'property'  =>  'title'
            ])
            ->add('ownershipStatus', null, [
                'label' =>  'Статус собственности'
            ])
            ->add('phone', null, [
                'label' =>  'Телефон'
            ])
            ->add('email', null, [
                'label' =>  'E-mail'
            ])
            ->add('location', null, [
                'label' =>  'Адрес места жительства/места нахождения'
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
                'label' =>  'ФИО/Наименование'
            ])
            ->add('debtorType', EntityType::class, [
                'label' =>  'Тип должника'
            ])
            ->add('ownershipStatus', EntityType::class, [
                'label' =>  'Статус собственности'
            ])
            ->add('phone', null, [
                'label' =>  'Телефон'
            ])
            ->add('email', null, [
                'label' =>  'E-mail'
            ])
            ->add('location', null, [
                'label' =>  'Адрес места жительства/места нахождения'
            ])
            ->add('_action', null, array(
                'lanel'     =>  'Действия',
                'actions'   => array(
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
            ->add('id')
            ->add('name')
            ->add('phone')
            ->add('email')
            ->add('location')
            ->add('startDateOwnership')
            ->add('endDateOwnership')
            ->add('startDebtPeriod')
            ->add('endDebtPeriod')
            ->add('dateFillDebt')
            ->add('sumDebt')
            ->add('periodAccruedDebt')
            ->add('periodPayDebt')
            ->add('dateFillFine')
            ->add('sumFine')
            ->add('periodAccruedFine')
            ->add('periodPayFine')
            ->add('arhive')
            ->add('dateOfBirth')
            ->add('placeOfBirth')
            ->add('ownerName')
            ->add('ogrnip')
            ->add('inn')
            ->add('ogrn')
            ->add('bossName')
            ->add('bossPosition')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('phone')
            ->add('email')
            ->add('location')
            ->add('startDateOwnership')
            ->add('endDateOwnership')
            ->add('startDebtPeriod')
            ->add('endDebtPeriod')
            ->add('dateFillDebt')
            ->add('sumDebt')
            ->add('periodAccruedDebt')
            ->add('periodPayDebt')
            ->add('dateFillFine')
            ->add('sumFine')
            ->add('periodAccruedFine')
            ->add('periodPayFine')
            ->add('arhive')
            ->add('dateOfBirth')
            ->add('placeOfBirth')
            ->add('ownerName')
            ->add('ogrnip')
            ->add('inn')
            ->add('ogrn')
            ->add('bossName')
            ->add('bossPosition')
        ;
    }
}
