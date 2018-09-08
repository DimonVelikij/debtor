<?php

namespace AppBundle\EventGenerator;

use AppBundle\Entity\Flat;
use AppBundle\Entity\FlatEvent;
use Doctrine\ORM\EntityManager;

class FinishGenerator
{
    /** @var EntityManager  */
    private $em;

    /**
     * FinishGenerator constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * удаление событий у помещения и зануление долгов
     * @param Flat $flat
     */
    public function finishAction(Flat $flat)
    {
        $flat
            ->setEventData(null)
            ->setSumDebt(0)
            ->setSumFine(null)
            ->setPeriodAccruedDebt(null)
            ->setPeriodAccruedFine(null);

        $this->em->persist($flat);
        $this->em->flush();

        /** @var FlatEvent $flatEvent */
        foreach ($flat->getFlatsEvents() as $flatEvent) {
            $this->em->remove($flatEvent);
        }

        $this->em->flush();
    }
}