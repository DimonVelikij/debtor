<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Template;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class TemplateAdmin extends AbstractAdmin
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
            ->add('slug', null, [
                'label' =>  'Алиас'
            ])
            ->add('timePerformAction', null, [
                'label' =>  'Через сколько дней выполнится'
            ])
            ->add('isStart', null, [
                'label' =>  'Стартовый'
            ])
            ->add('isJudicial', null, [
                'label' =>  'Судебный'
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
            ->add('slug', null, [
                'label' =>  'Алиас'
            ])
            ->add('timePerformAction', null, [
                'label' =>  'Через сколько дней выполнится'
            ])
            ->add('isStart', null, [
                'label' =>  'Стартовый'
            ])
            ->add('isJudicial', null, [
                'label' =>  'Судебный'
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
        /** @var Template $startTemplate */
        $startTemplate = $this->getDoctrine()->getRepository('AppBundle:Template')
            ->createQueryBuilder('template')
            ->where('template.isStart = :isStart')
            ->setMaxResults(1)
            ->setParameter('isStart', true)
            ->getQuery()
            ->getOneOrNullResult();

        $formMapper
            ->add('name', TextType::class, [
                'label'         =>  'Название',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' =>  'Укажите название шаблона'])
                ]
            ])
            ->add('slug', TextType::class, [
                'label'         =>  'Алиас',
                'required'      =>  false,
                'sonata_help'   =>  'Сгенерируется автоматически по полю "Название"',
                'disabled'      =>  true
            ])
            ->add('template', CKEditorType::class, [
                'label'         =>  'Шаблон',
                'required'      =>  false
            ])
            ->add('timePerformAction', NumberType::class, [
                'label'         =>  'Через какое количество дней выполнить',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' =>  'Укажите количество дней'])
                ]
            ])
            ->add('templateFields', null, [
                'label'         =>  'Список полей для шаблона',
                'required'      =>  false
            ])
            ->add('isJudicial', CheckboxType::class, [
                'label'         =>  'Является судебным',
                'required'      =>  false
            ]);

        if (!$startTemplate) {
            $formMapper
                ->add('isStart', CheckboxType::class, [
                    'label'         =>  'Является стартовым',
                    'required'      =>  true,
                    'constraints'   =>  [
                        new NotBlank(['message' =>  'Шаблон должен быть стартовым'])
                    ]
                ]);
        }
    }

    /**
     * @return null|\Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->getConfigurationPool()->getContainer();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getDoctrine()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
