<?php

namespace AppBundle\Service;

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
     * @param $message
     * @return Log
     */
    public function createLog(Flat $flat, $message)
    {
        $log = new Log();

        $log
            ->setDate(new \DateTime())
            ->setData($message)
            ->setIsRead(false)
            ->setFlat($flat);

        return $log;
    }

    /**
     * добавляем запись в таблицу logs, привязанную к помещению
     * @param Flat $flat
     * @param $message
     * @return Log
     */
    public function log(Flat $flat, $message)
    {
        $log = $this->createLog($flat, $message);

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