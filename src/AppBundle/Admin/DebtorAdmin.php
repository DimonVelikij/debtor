<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class DebtorAdmin extends AbstractAdmin
{
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
        ;
    }
}
