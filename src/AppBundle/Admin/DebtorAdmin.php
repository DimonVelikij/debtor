<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class DebtorAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('types', 'types');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
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
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
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
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
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
