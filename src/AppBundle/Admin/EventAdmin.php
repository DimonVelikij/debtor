<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Event;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
                'label' =>  'Через сколько дней выполнится следующее событие'
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
                'label' =>  'Через сколько дней выполнится следующее событие'
            ])
            ->add('isStart', null, [
                'label' =>  'Стартовый'
            ])
            ->add('isJudicial', null, [
                'label' =>  'Судебный'
            ])
            ->add('parent', null, [
                'label' =>  'Родительское событие'
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
        /** @var Event $object */
        $object = $this->getSubject();

        /** @var array $templateFields */
        $templateFields = $this->getTemplateGenerator()->getTemplateFields();//поля подстановки для шаблонов

        /** @var Event|null $startEvent */
        $startEvent = $this->getDoctrine()->getRepository('AppBundle:Event')
            ->createQueryBuilder('event')
            ->where('event.isStart = :isStart')
            ->setMaxResults(1)
            ->setParameter('isStart', true)
            ->getQuery()
            ->getOneOrNullResult();//стартовое событие

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
            ->add('templateFields', ChoiceType::class, [
                'label'         =>  'Список полей для шаблона',
                'required'      =>  false,
                'choices'       =>  $templateFields,
                'multiple'      =>  true
            ])
            ->add('template', CKEditorType::class, [
                'label'         =>  'Шаблон',
                'required'      =>  false
            ])
            ->add('timePerformAction', NumberType::class, [
                'label'         =>  'Через какое количество дней выполнить следующее событие',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' =>  'Укажите количество дней'])
                ]
            ])
            ->add('isJudicial', CheckboxType::class, [
                'label'         =>  'Является судебным',
                'required'      =>  false
            ])
            ->add('parent', EntityType::class, [
                'label'         =>  'Родительское событие',
                'required'      =>  false,
                'class'         =>  Event::class,
                'query_builder' =>  function (EntityRepository $er) use ($object) {
                    /** @var QueryBuilder $eventQueryBuilder */
                    $eventQueryBuilder = $er->createQueryBuilder('event');

                    //выводим события текущее родительское или у которых родительское поле пустое и не является стартовым
                    return $eventQueryBuilder
                        ->where(
                            $eventQueryBuilder->expr()->orX(
                                'event.id = :currentParent',
                                $eventQueryBuilder->expr()->andX(
                                    $eventQueryBuilder->expr()->isNull('event.parent'),
                                    $eventQueryBuilder->expr()->orX(
                                        'event.isStart != :isStart',
                                        $eventQueryBuilder->expr()->isNull('event.isStart')
                                    )
                                )
                            )
                        )
                        ->setParameters([
                            'isStart' => 1,
                            'currentParent' => $object->getParent() ? $object->getParent()->getId() : null
                        ]);
                }
            ])
        ;

        if (!$startEvent || $object->getIsStart()) {
            $formMapper
                ->add('isStart', CheckboxType::class, [
                    'label'         =>  'Является стартовым',
                    'required'      =>  false
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

    /**
     * @return \AppBundle\Service\TemplateGenerator|object
     */
    private function getTemplateGenerator()
    {
        return $this->getContainer()->get('app.service.template_generator');
    }
}
