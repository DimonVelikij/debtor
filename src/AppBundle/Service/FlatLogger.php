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
     * добавляем запись в таблицу logs, привязанную к помещению
     * @param Flat $flat
     * @param $message
     */
    public function log(Flat $flat, $message)
    {
        $log = new Log();

        $log
            ->setDate(new \DateTime())
            ->setData($message)
            ->setIsRead(false)
            ->setFlat($flat);

        $this->em->persist($log);
        $this->em->flush();
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
}