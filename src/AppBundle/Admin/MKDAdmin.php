<?php

namespace AppBundle\Admin;

use AppBundle\Validator\Constraints\MKDExist;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class MKDAdmin extends AbstractAdmin
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
            ->add('houseNumber', null, [
                'label' =>  'Номер дома'
            ])
            ->add('managementStartDate', null ,[
                'label' =>  'Дата начала управления'
            ])
            ->add('managementEndDate', null, [
                'label' =>  'Дата окончания управления'
            ])
            ->add('legalDocumentName', null, [
                'label' =>  'Название документа на право управления'
            ])
            ->add('legalDocumentDate', null, [
                'label' =>  'Дата документа начала управления'
            ])
            ->add('legalDocumentNumber', null, [
                'label' =>  'Номер документа начала управления'
            ])
            ->add('judicialSector', null, [
                'label' =>  'Судебный участок'
            ])
            ->add('fsspDepartment', null, [
                'label' =>  'Отделение ФССП'
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
            ->add('houseNumber', null, [
                'label' =>  'Номер дома'
            ])
            ->add('managementStartDate', null ,[
                'label' =>  'Дата начала управления',
                'format'=>  'd.m.Y'
            ])
            ->add('managementEndDate', null, [
                'label' =>  'Дата окончания управления',
                'format'=>  'd.m.Y'
            ])
            ->add('legalDocumentName', null, [
                'label' =>  'Название документа на право управления'
            ])
            ->add('legalDocumentDate', null, [
                'label' =>  'Дата документа начала управления',
                'format'=>  'd.m.Y'
            ])
            ->add('legalDocumentNumber', null, [
                'label' =>  'Номер документа начала управления'
            ])
            ->add('judicialSector', null, [
                'label' =>  'Судебный участок'
            ])
            ->add('fsspDepartment', null, [
                'label' =>  'Отделение ФССП'
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
            ->add('street', 'entity', [
                'label'         =>  'Город, улица',
                'required'      =>  true,
                'class'         =>  'AppBundle\Entity\Street',
                'group_by'      =>  'city',
                'help'          =>  "<span style='color: blue;'>Если в списке нет нужной улицы, необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_street_create')}'>добавить улицу</a> и обновить страницу</span>",
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите название улицы'])
                ]
            ])
            ->add('houseNumber', TextType::class, [
                'label'         =>  'Номер дома',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите номер дома']),
                    new MKDExist()
                ]
            ])
            ->add('managementStartDate', DateType::class, [
                'label'         =>  'Дата начала управления',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите дату начала управления'])
                ],
                'widget'        => 'single_text'
            ])
            ->add('managementEndDate', DateType::class, [
                'label'         =>  'Дата окнчания управления',
                'required'      =>  false,
                'widget'        => 'single_text'
            ])
            ->add('legalDocumentName', TextType::class, [
                'label'         =>  'Название документа на право управления',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите название документа'])
                ]
            ])
            ->add('legalDocumentDate', DateType::class, [
                'label'         =>  'Дата документа начала управления',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите дату документа'])
                ],
                'widget'        => 'single_text'
            ])
            ->add('legalDocumentNumber', TextType::class, [
                'label'         =>  'Номер документа начала управления',
                'required'      =>  false
            ])
            ->add('judicialSector', 'entity', [
                'label'         =>  'Судебный участок',
                'required'      =>  true,
                'class'         =>  'AppBundle\Entity\JudicialSector',
                'help'          =>  "<span style='color: blue;'>Если в списке нет нужного судебного участка, необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_judicialsector_create')}'>добавить судебный участок</a> и обновить страницу</span>",
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите судебный участок'])
                ]
            ])
            ->add('fsspDepartment', 'entity', [
                'label'         =>  'Отделение ФССП',
                'required'      =>  true,
                'class'         =>  'AppBundle\Entity\FSSPDepartment',
                'help'          =>  "<span style='color: blue;'>Если в списке нет нужного отделения ФССП, необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_fsspdepartment_create')}'>добавить отделение ФССП</a> и обновить страницу</span>",
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите судебный участок'])
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
            ->add('houseNumber', null, [
                'label' =>  'Номер дома'
            ])
            ->add('managementStartDate', null ,[
                'label' =>  'Дата начала управления',
                'format'=>  'd.m.Y'
            ])
            ->add('managementEndDate', null, [
                'label' =>  'Дата окончания управления',
                'format'=>  'd.m.Y'
            ])
            ->add('legalDocumentName', null, [
                'label' =>  'Название документа на право управления'
            ])
            ->add('legalDocumentDate', null, [
                'label' =>  'Дата документа начала управления',
                'format'=>  'd.m.Y'
            ])
            ->add('legalDocumentNumber', null, [
                'label' =>  'Номер документа начала управления'
            ])
            ->add('judicialSector', null, [
                'label' =>  'Судебный участок'
            ])
            ->add('fsspDepartment', null, [
                'label' =>  'Отделение ФССП'
            ])
        ;
    }

    /**
     * @return object|\Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private function getRouter()
    {
        return $this->getConfigurationPool()->getContainer()->get('router');
    }
}
