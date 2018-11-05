<?php

namespace AppBundle\Twig;

use AppBundle\Entity\FlatEvent;
use AppBundle\EventGenerator\GeneratorAggregate;
use AppBundle\EventGenerator\Generator\GeneratorInterface;

class EventExtension extends \Twig_Extension
{
    /** @var GeneratorAggregate  */
    private $generatorAggregate;

    /**
     * EventExtension constructor.
     * @param GeneratorAggregate $generatorAggregate
     */
    public function __construct(GeneratorAggregate $generatorAggregate)
    {
        $this->generatorAggregate = $generatorAggregate;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_next_event', [$this, 'getNextEvent'])
        ];
    }

    /**
     * @param FlatEvent $flatEvent
     * @return FlatEvent|null
     */
    public function getNextEvent(FlatEvent $flatEvent)
    {
        /** @var GeneratorInterface $currentEventGenerator */
        $currentEventGenerator = $this
            ->generatorAggregate
            ->getEventGenerator($flatEvent->getEvent()->getAlias());

        $nextEvent = $currentEventGenerator->getNextEvent($flatEvent);

        return $nextEvent ?
            //если удалось определить следующее событие - создаем объект FlatEvent
            (new FlatEvent())
                ->setFlat($flatEvent->getFlat())
                ->setEvent($nextEvent)
                ->setDateGenerate($flatEvent->getDateGenerate()->modify("+{$currentEventGenerator->getTimePerformAction($flatEvent)} day")) :
            null;
    }
}