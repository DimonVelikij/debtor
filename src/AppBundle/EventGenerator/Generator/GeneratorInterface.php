<?php

namespace AppBundle\EventGenerator\Generator;

use AppBundle\Entity\Event;
use AppBundle\Entity\FlatEvent;
use Symfony\Component\HttpFoundation\Request;

interface GeneratorInterface
{
    /**
     * alias текущего события
     * @return string
     */
    public function getEventAlias();

    /**
     * через сколько дней выполнить следующее событие
     * @param FlatEvent $flatEvent
     * @return integer
     */
    public function getTimePerformAction(FlatEvent $flatEvent);

    /**
     * генерация события
     * @param FlatEvent $flatEvent
     * @return bool
     */
    public function generateEvent(FlatEvent $flatEvent);

    /**
     * добавление следующиего генератора
     * @param GeneratorInterface $generator
     */
    public function addNextEventGenerator(GeneratorInterface $generator);

    /**
     * получение списка следующих генераторов
     * @param FlatEvent $flatEvent
     * @return array
     */
    public function getNextEventGenerators(FlatEvent $flatEvent);

    /**
     * обработка действий пользователя
     * @param Request $request
     * @return bool
     */
    public function processUserAction(Request $request);

    /**
     * получение следующего события
     * @param FlatEvent $flatEvent
     * @return Event|null
     */
    public function getNextEvent(FlatEvent $flatEvent);

    /**
     * пропустить событие
     * @param Request $request
     * @return bool
     */
    public function miss(Request $request);

    /**
     * выполнить событие не дожидаясь времени выполнения
     * @param Request $request
     * @return bool
     */
    public function perform(Request $request);
}