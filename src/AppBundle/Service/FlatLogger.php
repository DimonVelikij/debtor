<?php

namespace AppBundle\Service;

use AppBundle\Entity\Event;
use AppBundle\Entity\Flat;
use AppBundle\Entity\Log;
use Doctrine\ORM\EntityManager;

class FlatLogger
{
    /** @var EntityManager  */
    private $em;

    /**
     * Logger constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * создание объекта лога
     * @param Flat $flat
     * @param string $message
     * @param Event|null $event
     * @return Log
     */
    public function createLog(Flat $flat, string $message, Event $event = null)
    {
        $log = new Log();

        $log
            ->setDate(new \DateTime())
            ->setData($message)
            ->setEvent($event ? $event->getAlias() : null)
            ->setIsRead(false)
            ->setFlat($flat);

        return $log;
    }

    /**
     * добавляем запись в таблицу logs, привязанную к помещению
     * @param Flat $flat
     * @param $message
     * @param Event|null $event
     * @return Log
     */
    public function log(Flat $flat, $message, Event $event = null)
    {
        $log = $this->createLog($flat, $message, $event);

        $this->em->persist($log);
        $this->em->flush();

        return $log;
    }

    /**
     * получаем префикс для лога
     * @param Flat $flat
     * @return string
     */
    public function getLogPrefix(Flat $flat)
    {
        $currentDate = new \DateTime();

        return $currentDate->format('d.m.Y H:i') .
            ': Помещение #' . $flat->getId() . ' Адрес: ' .
            $flat->getHouse()->getStreet()->getCity()->getTitle() . ' ' .
            $flat->getHouse()->getStreet()->getTitle() . ' ' .
            $flat->getHouse()->getNumber() . ' Помещение №' .
            $flat->getNumber() . '. ';
    }

    /**
     * получения разделителя между логами
     * @return string
     */
    public function getDelimiter()
    {
        return "==================================================\n";
    }
}