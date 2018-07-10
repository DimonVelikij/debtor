<?php

namespace AppBundle\EventGenerator\Generator;

use AppBundle\Entity\Flat;
use AppBundle\Entity\FlatEvent;
use AppBundle\Service\FlatLogger;
use AppBundle\Service\TemplateGenerator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Request;

class ApplyingCourtOrderGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * ApplyingCourtOrderGenerator constructor.
     * @param EntityManager $em
     * @param FlatLogger $flatLogger
     * @param Router $router
     * @param TemplateGenerator $templateGenerator
     * @param TwigEngine $templating
     */
    public function __construct(EntityManager $em, FlatLogger $flatLogger, Router $router, TemplateGenerator $templateGenerator, TwigEngine $templating)
    {
        parent::__construct($em, $flatLogger, $router, $templateGenerator, $templating);
    }

    /**
     * @return string
     */
    public function getEventAlias()
    {
        return 'applying_court_order';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        //если подача заявления подтверждена, то можно приступать к следующему событию, иначе нельзя
        return $flatEvent->getParameter('confirmed') ? 40 : INF;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function processUserAction(Request $request)
    {
        /** @var Flat $flat */
        $flat = $this->em->getRepository('AppBundle:Flat')->find((int)$request->get('flat_id'));

        //находим текущее событие
        $currentFlatEvent = null;

        /** @var FlatEvent $flatEvent */
        foreach ($flat->getFlatsEvents() as $flatEvent) {
            if ($flatEvent->getEvent()->getAlias() == $this->getEventAlias()) {
                $currentFlatEvent = $flatEvent;
                break;
            }
        }

        $currentData = new \DateTime();
        $showData = "Подтверждена {$currentData->format('d.m.Y')}";

        $currentFlatEvent
            ->setDateGenerate($currentData)
            ->setData([
                'show'      =>  $showData,
                'confirmed' =>  true//подтверждено - можно через 40 дней выполнять следующее событие
            ]);

        $this->em->persist($currentFlatEvent);
        $this->em->flush();

        //добавляем лог - что все подтверждения подача заявления на СП в суд
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

        return true;
    }

    /**
     * @param Flat $flat
     * @param FlatEvent $flatEvent
     * @return bool
     */
    public function eventGenerate(Flat $flat, FlatEvent $flatEvent)
    {
        $showData = "<a href='{$this->router->generate('admin_app_flat_process_user', ['event' => $this->event->getAlias(), 'flat_id' => $flat->getId()])}'>Подтвердить подачу заявления СП в суд</a>";

        //удаляем событие "Формирование заявления на выдачу судебного приказа"
        $this->em->remove($flatEvent);

        //добавляем событие "Подача заявления на выдачу судебного приказа"
        $executeFlatEvent = new FlatEvent();
        $executeFlatEvent
            ->setFlat($flat)
            ->setDateGenerate(new \DateTime())
            ->setEvent($this->event)
            ->setData([
                'show'      =>  $showData,
                'confirmed' =>  false//подача заявления не подтверждена, следующее событие нельзя выполнять до подтверждения
            ]);

        $this->em->persist($executeFlatEvent);
        $this->em->flush();

        //добавляем лог - сгенерировалось событие "Подача заявления на выдачу судебного приказа"
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

        return true;
    }
}