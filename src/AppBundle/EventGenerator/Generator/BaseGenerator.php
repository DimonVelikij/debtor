<?php

namespace AppBundle\EventGenerator\Generator;

use AppBundle\Entity\Event;
use AppBundle\Entity\FlatEvent;
use AppBundle\Exception\NoTemplateEventException;
use AppBundle\Exception\NoTemplateFieldsEventException;
use AppBundle\Service\FlatLogger;
use AppBundle\Service\TemplateGenerator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseGenerator
{
    /** @var EntityManager  */
    protected $em;

    /** @var FlatLogger  */
    protected $flatLogger;

    /** @var Router  */
    protected $router;

    /** @var TemplateGenerator  */
    protected $templateGenerator;

    /** @var TwigEngine  */
    protected $twig;

    /** @var ValidatorInterface  */
    protected $validator;

    /** @var DataCollectorTranslator  */
    protected $translator;

    /** @var  Event */
    protected $event;

    /** @var array  */
    protected $nextEventGenerators = [];

    /**
     * BaseGenerator constructor.
     * @param EntityManager $em
     * @param FlatLogger $flatLogger
     * @param Router $router
     * @param TemplateGenerator $templateGenerator
     * @param TwigEngine $twig
     * @param ValidatorInterface $validator
     * @param DataCollectorTranslator $translator
     */
    public function __construct(EntityManager $em, FlatLogger $flatLogger, Router $router, TemplateGenerator $templateGenerator, TwigEngine $twig, ValidatorInterface $validator, DataCollectorTranslator $translator)
    {
        $this->em = $em;
        $this->flatLogger = $flatLogger;
        $this->router = $router;
        $this->templateGenerator = $templateGenerator;
        $this->twig = $twig;
        $this->validator = $validator;
        $this->translator = $translator;
        $this->event = $this->em->getRepository('AppBundle:Event')->findOneBy(['alias' => $this->getEventAlias()]);
    }

    /**
     * добавление следующиего генератора
     * @param GeneratorInterface $generator
     */
    public function addNextEventGenerator(GeneratorInterface $generator)
    {
        $this->nextEventGenerators[] = $generator;
    }

    /**
     * получение списка следующих генераторов
     * @param FlatEvent $flatEvent
     * @return array
     */
    public function getNextEventGenerators(FlatEvent $flatEvent)
    {
        return $this->nextEventGenerators;
    }

    /**
     * валидация событий с шаблонами
     * @throws NoTemplateEventException
     * @throws NoTemplateFieldsEventException
     */
    public function validateEvent()
    {
        if (!$this->event->getTemplate()) {
            throw new NoTemplateEventException("Отсутствует шаблон у события '{$this->event->getName()}'. Обратитесь к администратору.");
        }

        if (!$this->event->getTemplateFields()) {
            throw new NoTemplateFieldsEventException("Отсутствуют поля подстановки для шаблона у события '{$this->event->getName()}'. Обратитесь к администратору.");
        }
    }

    /**
     * alias текущего события
     * @return string
     */
    abstract public function getEventAlias();
}