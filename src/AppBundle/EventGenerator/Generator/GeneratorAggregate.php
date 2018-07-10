<?php

namespace AppBundle\EventGenerator\Generator;

use AppBundle\Entity\Flat;
use AppBundle\Entity\FlatEvent;
use AppBundle\Exception\NoDebtorsException;
use AppBundle\Exception\NoSubscribersException;
use AppBundle\Exception\NoTemplateEventException;
use AppBundle\Exception\NoTemplateFieldsEventException;
use AppBundle\Service\FlatLogger;
use Doctrine\ORM\EntityManager;

class GeneratorAggregate
{
    private $eventGenerators = [];

    /** @var EntityManager  */
    private $em;

    /** @var FlatLogger  */
    private $flatLogger;

    /**
     * GeneratorAggregate constructor.
     * @param EntityManager $em
     * @param FlatLogger $flatLogger
     */
    public function __construct(EntityManager $em, FlatLogger $flatLogger)
    {
        $this->em = $em;
        $this->flatLogger = $flatLogger;
    }

    /**
     * @param GeneratorInterface $generator
     * @param $generatorAlias
     */
    public function addEventGenerator(GeneratorInterface $generator, $generatorAlias)
    {
        $this->eventGenerators[$generatorAlias] = $generator;
    }

    public function processFlat(Flat $flat)
    {
        //проверка на долг
        /** @var FlatEvent $flatEvent */
        foreach ($flat->getFlatsEvents() as $flatEvent) {
            /** @var GeneratorInterface $currentEventGenerator */
            $currentEventGenerator = $this->eventGenerators[$flatEvent->getEvent()->getAlias()];

            //если время не пришло
            /*if ((new \DateTime())->diff($flatEvent->getDateGenerate())->d < $currentEventGenerator->getTimePerformAction($flatEvent)) {
                continue;
            }*/

            $this->eventGenerate($flat, $flatEvent);
        }
    }

    /**
     * @param Flat $flat
     * @param FlatEvent $flatEvent
     */
    public function eventGenerate(Flat $flat, FlatEvent $flatEvent)
    {
        try {
            /** @var GeneratorInterface $currentEventGenerator */
            $currentEventGenerator = $this->eventGenerators[$flatEvent->getEvent()->getAlias()];

            /** @var GeneratorInterface $nextEventGenerator */
            foreach ($currentEventGenerator->getNextEventGenerators($flatEvent) as $nextEventGenerator) {
                $nextEventGenerator->eventGenerate($flat, $flatEvent);
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
            //если ошибка выполнения программы - ставим признак ошибки у помещения
            $flat->setIsGenerateErrors(true);
        }

        $this->em->persist($flat);
        $this->em->flush();
    }
}