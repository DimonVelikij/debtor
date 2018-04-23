<?php

namespace AppBundle\Admin;

use AppBundle\Entity\House;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class FlatAdmin extends AbstractAdmin
{
    /**
     * @param $name
     * @return mixed|null|string
     */
    public function getTemplate($name)
    {
         $this->templates['edit'] = 'AppBundle:Admin\Flat:edit.html.twig';

         return parent::getTemplate($name);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('debtors', 'debtors')
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('number')
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
            ->add('archive')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('number')
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
            ->add('archive')
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
        $houses = $this->getEntityManager()->getRepository('AppBundle:House')
            ->createQueryBuilder('house')
            ->innerJoin('house.street', 'street')
            ->orderBy('street.city')
            ->getQuery()
            ->getResult();

        $houseHelp = count($houses) ?
            "<span style='color: blue;'>Если в списке нет нужного дома, необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_house_create')}'>добавить дом</a> и обновить страницу</span>" :
            "<span style='color: red'>Список домов пуст. Необходимо <a target='_blank' href='{$this->getRouter()->generate('admin_app_house_create')}'>добавить дом</a> и обновить страницу</span>";

        $houseChoice = [];

        /** @var House $house */
        foreach ($houses as $house) {
            if (!isset($houseChoice[$house->getStreet()->getCity()->getTitle() . ', ' . $house->getStreet()->getTitle()])) {
                $houseChoice[$house->getStreet()->getCity()->getTitle() . ', ' . $house->getStreet()->getTitle()] = [];
            }
            $houseChoice[$house->getStreet()->getCity()->getTitle() . ', ' . $house->getStreet()->getTitle()][$house->getStreet()->getCity()->getTitle() . ', ' . $house->getStreet()->getTitle() . ', ' . $house->getNumber()] = $house;
        }

        $formMapper
            ->with('Помещение')
                ->add('house', ChoiceType::class, [
                    'label'         =>  'Дом',
                    'choices'       =>  $houseChoice,
                    'required'      =>  true,
                    'help'          =>  $houseHelp,
                    'constraints'   =>  [
                        new NotBlank(['message' => 'Укажите дом'])
                    ]
                ])
                ->add('number', TextType::class, [
                    'label'         =>  'Номер помещения',
                    'required'      =>  true,
                    'constraints'   =>  [
                        new NotBlank(['message' => 'Укажите номер помещения'])
                    ]
                ])
                ->add('archive', CheckboxType::class, [
                    'label'         =>  'Больше не является должником (Отправить в архив)',
                    'required'      =>  false
                ])
            ->end()
            ->with('Период взыскания')
                ->add('startDebtPeriod', DateType::class, [
                    'label'     =>  'Начало периода взыскания',
                    'required'  =>  false,
                    'widget'    => 'single_text'
                ])
                ->add('endDebtPeriod', DateType::class, [
                    'label'     =>  'Конец периода взыскания',
                    'required'  =>  false,
                    'widget'    => 'single_text'
                ])
            ->end()
            ->with('Основной долг', ['class' => 'col-md-6'])
                ->add('dateFillDebt', DateType::class, [
                    'label'     =>  'Дата заполнения основного долга',
                    'required'  =>  false,
                    'widget'    => 'single_text'
                ])
                ->add('sumDebt', TextType::class, [
                    'label'     =>  'Сумма долга',
                    'required'  =>  false
                ])
                ->add('periodAccruedDebt', TextType::class, [
                    'label'         =>  'За период начислено',
                    'required'      =>  true,
                    'constraints'   =>  [
                        new NotBlank(['message' => 'Укажите сумму начисления долга'])
                    ]
                ])
                ->add('periodPayDebt', TextType::class, [
                    'label'         =>  'За период оплачено',
                    'required'      =>  true,
                    'constraints'   =>  [
                        new NotBlank(['message' => 'Укажите сумму оплаченного долга'])
                    ]
                ])
            ->end()
            ->with('Пени', ['class' => 'col-md-6'])
                ->add('dateFillFine', DateType::class, [
                    'label'     =>  'Дата заполнения пени',
                    'required'  =>  false,
                    'widget'    => 'single_text'
                ])
                ->add('sumFine', TextType::class, [
                    'label'     =>  'Сумма долга',
                    'required'  =>  false
                ])
                ->add('periodAccruedFine', TextType::class, [
                    'label'         =>  'За период начислено',
                    'required'      =>  true,
                    'constraints'   =>  [
                        new NotBlank(['message' => 'Укажите сумму начисления пени'])
                    ]
                ])
                ->add('periodPayFine', TextType::class, [
                    'label'         =>  'За период оплачено',
                    'required'      =>  true,
                    'constraints'   =>  [
                        new NotBlank(['message' => 'Укажите сумму оплаченных пени'])
                    ]
                ])
            ->end()
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('number')
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
            ->add('archive')
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
