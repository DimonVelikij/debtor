<?php

namespace AppBundle\Form\Admin;

use AppBundle\Entity\Event;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SignatureType extends AbstractType
{
    /** @var EntityManager  */
    private $em;

    /**
     * SignatureType constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var QueryBuilder $eventQueryBuilder */
        $eventQueryBuilder = $this->em->getRepository('AppBundle:Event')->createQueryBuilder('event');

        /** @var Event[] $templates */
        $templates = $eventQueryBuilder
            ->where($eventQueryBuilder->expr()->isNotNull('event.template'))
            ->andWhere($eventQueryBuilder->expr()->isNotNull('event.templateFields'))
            ->getQuery()
            ->getResult();

        array_unshift($templates, (new Event())
            ->setAlias('default')
            ->setName('Все шаблоны')
        );

        $templateChoice = [];

        foreach ($templates as $template) {
            $templateChoice[$template->getName()] = $template->getAlias();
        }

        $builder
            ->add('name', TextType::class, [
                'label'         =>  'ФИО',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите ФИО'])
                ]
            ])
            ->add('position', TextType::class, [
                'label'         =>  'Должность',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите должность'])
                ]
            ])
            ->add('document', TextType::class, [
                'label'         =>  'Документ, подтверждающий полномочия',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите документ, подтверждающий полномочия'])
                ]
            ])
            ->add('event', ChoiceType::class, [
                'label'         =>  'Шаблон',
                'required'      =>  true,
                'constraints'   =>  [
                    new NotBlank(['message' => 'Укажите шаблон'])
                ],
                'choices'       =>  $templateChoice
            ]);
    }
}