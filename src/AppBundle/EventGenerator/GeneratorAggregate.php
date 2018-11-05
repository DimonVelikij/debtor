<?php

namespace AppBundle\EventGenerator;

use AppBundle\Entity\Flat;
use AppBundle\Entity\FlatEvent;
use AppBundle\EventGenerator\Generator\GeneratorInterface;
use AppBundle\Exception\NoDebtorsException;
use AppBundle\Exception\NoSubscribersException;
use AppBundle\Exception\NoTemplateEventException;
use AppBundle\Exception\NoTemplateFieldsEventException;
use AppBundle\Service\DateDiffer;
use AppBundle\Service\FlatLogger;
use Doctrine\ORM\EntityManager;

class GeneratorAggregate
{
    /** сумма долга, после которой на должника оформляются документы */
    const TOTAL_DEBT = 5000;

    private $eventGenerators = [];

    /** @var EntityManager  */
    private $em;

    /** @var FlatLogger  */
    private $flatLogger;

    /** @var DateDiffer  */
    private $dateDiffer;

    /**
     * GeneratorAggregate constructor.
     * @param EntityManager $em
     * @param FlatLogger $flatLogger
     * @param DateDiffer $dateDiffer
     */
    public function __construct(EntityManager $em, FlatLogger $flatLogger, DateDiffer $dateDiffer)
    {
        $this->em = $em;
        $this->flatLogger = $flatLogger;
        $this->dateDiffer = $dateDiffer;
    }

    /**
     * @param $eventAlias
     * @return GeneratorInterface|null
     */
    public function getEventGenerator($eventAlias)
    {
        return $this->eventGenerators[$eventAlias] ?? null;
    }

    /**
     * @param GeneratorInterface $generator
     * @param $generatorAlias
     */
    public function addEventGenerator(GeneratorInterface $generator, $generatorAlias)
    {
        $this->eventGenerators[$generatorAlias] = $generator;
    }

    /**
     * @param Flat $flat
     */
    public function processFlat(Flat $flat)
    {
        //если сумма долга меньше 5000 - не формируем документы на должника
        if ($flat->getSumDebt() + $flat->getSumFine() < self::TOTAL_DEBT) {
            return;
        }

        //проверка на долг
        /** @var FlatEvent $flatEvent */
        foreach ($flat->getFlatsEvents() as $flatEvent) {
            /** @var GeneratorInterface $currentEventGenerator */
            $currentEventGenerator = $this->eventGenerators[$flatEvent->getEvent()->getAlias()];

            //если время не пришло
            if ($this->dateDiffer->getDays($flatEvent->getDateGenerate(), new \DateTime()) < $currentEventGenerator->getTimePerformAction($flatEvent)) {
                continue;
            }

            $this->generateEvent($flatEvent);
        }
    }

    /**
     * @param FlatEvent $flatEvent
     */
    public function generateEvent(FlatEvent $flatEvent)
    {
        /** @var Flat $flat */
        $flat = $flatEvent->getFlat();

        try {
            /** @var GeneratorInterface $currentEventGenerator */
            $currentEventGenerator = $this->eventGenerators[$flatEvent->getEvent()->getAlias()];

            /** @var array $nextEventGenerators */
            $nextEventGenerators = $currentEventGenerator->getNextEventGenerators($flatEvent);

            //если генераторов нет - ничего не делаем
            if (!count($nextEventGenerators)) {
                return;
            }

            /** @var GeneratorInterface $nextEventGenerator */
            foreach ($nextEventGenerators as $nextEventGenerator) {
                $nextEventGenerator->generateEvent($flatEvent);
            }
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
    }
}