<?php

namespace AppBundle\EventGenerator\Generator;

use AppBundle\Entity\Event;
use AppBundle\Entity\Flat;
use AppBundle\Entity\FlatEvent;
use AppBundle\Exception\NoDebtorsException;
use AppBundle\Exception\NoSubscribersException;
use AppBundle\Exception\NoTemplateEventException;
use AppBundle\Exception\NoTemplateFieldsEventException;
use AppBundle\Service\DateDiffer;
use AppBundle\Service\FlatLogger;
use AppBundle\Service\TemplateGenerator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Request;
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

    /** @var DateDiffer  */
    protected $dateDiffer;

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
     * @param DateDiffer $dateDiffer
     * @param TwigEngine $twig
     * @param ValidatorInterface $validator
     * @param DataCollectorTranslator $translator
     */
    public function __construct(EntityManager $em, FlatLogger $flatLogger, Router $router, TemplateGenerator $templateGenerator, DateDiffer $dateDiffer, TwigEngine $twig, ValidatorInterface $validator, DataCollectorTranslator $translator)
    {
        $this->em = $em;
        $this->flatLogger = $flatLogger;
        $this->router = $router;
        $this->templateGenerator = $templateGenerator;
        $this->dateDiffer = $dateDiffer;
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
     * выполнить действие не дожидаясь времени выполнения
     * @param Request $request
     * @return bool
     */
    public function perform(Request $request)
    {
        /** @var FlatEvent|null $flatEvent */
        $flatEvent = $this->getFlatEvent($request->get('flat_id'));

        if (!$flatEvent) {
            return false;
        }

        /** @var Flat $flat */
        $flat = $flatEvent->getFlat();

        /** @var FlatEvent|null $nextEvent */
        $nextFlatEvent = $this->getNextEvent($flatEvent);

        if (!$nextFlatEvent) {
            return false;
        }

        $nextEventGenerator = null;

        /** @var GeneratorInterface $eventGenerator */
        foreach ($this->getNextEventGenerators($flatEvent) as $eventGenerator) {
            if ($eventGenerator->getEventAlias() === $nextFlatEvent->getAlias()) {
                $nextEventGenerator = $eventGenerator;
                break;
            }
        }

        if (!$nextEventGenerator) {
            return false;
        }

        try {
            return $nextEventGenerator->generateEvent($flatEvent);
        } catch (NoTemplateEventException $e) {
            $this->flatLogger->log($flat, $e->getMessage());
        } catch (NoTemplateFieldsEventException $e) {
            $this->flatLogger->log($flat, $e->getMessage());
        } catch (NoSubscribersException $e) {
            $this->flatLogger->log($flat, $e->getMessage());
            $flat->setIsGenerateErrors(true);
        } catch (NoDebtorsException $e) {
            $this->flatLogger->log($flat, $e->getMessage());
            $flat->setIsGenerateErrors(true);
        } catch (\Exception $e) {
            $this->flatLogger->log($flat, "Ошибка: 'File:{$e->getFile()}. Line:{$e->getLine()}. Error:{$e->getMessage()}'. Событие:{$flatEvent->getEvent()->getName()}");
        }

        $this->em->persist($flat);
        $this->em->flush();

        return true;
    }

    /**
     * пропустить действие
     * @param Request $request
     * @return bool
     */
    public function miss(Request $request)
    {
        /** @var FlatEvent|null $flatEvent */
        $flatEvent = $this->getFlatEvent($request->get('flat_id'));

        if (!$flatEvent) {
            return false;
        }

        /** @var Event|null $nextEvent */
        $nextEvent = $this->getNextEvent($flatEvent);

        if (!$nextEvent) {
            return false;
        }

        //удаляем текущее событие
        $this->em->remove($flatEvent);
        $this->em->flush();

        //добавляем следующее событие
        $currentFlatEvent = new FlatEvent();
        $currentFlatEvent
            ->setFlat($flatEvent->getFlat())
            ->setDateGenerate(new \DateTime())
            ->setEvent($nextEvent)
            ->setData($this->getMissData());

        $this->em->persist($currentFlatEvent);
        $this->em->flush();

        //добавляем лог - пропущено событие
        $this->flatLogger->log($flatEvent->getFlat(), "<b>{$nextEvent->getName()}</b><br>Пропущено");

        return true;
    }

    /**
     * получение объъекта flatEvent по id помещения
     * @param $flatId
     * @return FlatEvent|null
     */
    protected function getFlatEvent($flatId)
    {
        /** @var Flat $flat */
        $flat = $this->em->getRepository('AppBundle:Flat')->findOneBy(['id' => $flatId]);

        /** @var FlatEvent $flatEvent */
        foreach ($flat->getFlatsEvents() as $flatEvent) {
            if ($flatEvent->getEvent()->getAlias() === $this->getEventAlias()) {
                return $flatEvent;
            }
        }

        return null;
    }

    /**
     * даные для поля data flatEvent'а
     * @return array
     */
    protected function getMissData()
    {
        return [
            'show'  =>  'Пропущено'
        ];
    }

    /**
     * alias текущего события
     * @return string
     */
    abstract public function getEventAlias();

    /**
     * получение следующего события
     * @param FlatEvent $flatEvent
     * @return Event|null
     */
    abstract public function getNextEvent(FlatEvent $flatEvent);
}