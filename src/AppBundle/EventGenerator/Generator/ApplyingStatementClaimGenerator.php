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

class ApplyingStatementClaimGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * ApplyingStatementClaimGenerator constructor.
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
        return 'applying_statement_claim';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        //если подача искового заявления подтверждена, то можно приступать к следующему событию, иначе нельзя
        return $flatEvent->getParameter('confirm') ? 7 : INF;
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

        if (
            !$currentFlatEvent ||
            $currentFlatEvent->getParameter('confirm', false)
        ) {
            //уже подтверждено
            return true;
        }

        $currentData = new \DateTime();
        $showData = "Подтверждено {$currentData->format('d.m.Y')}";

        $currentFlatEvent
            ->setDateGenerate($currentData)
            ->setData([
                'show'      =>  $showData,
                'confirm'   =>  true//подтверждено - можно через 7 дней выполнять следующее событие
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
        $showData = "<a href='{$this->router->generate('admin_app_flat_process_user', ['event' => $this->event->getAlias(), 'flat_id' => $flat->getId()])}'>Подтвердить подачу заявления искового заявления в суд</a>";

        //удаляем событие "Формирование искового заявления"
        $this->em->remove($flatEvent);

        //добавляем событие "Подача искового заявления"
        $executeFlatEvent = new FlatEvent();
        $executeFlatEvent
            ->setFlat($flat)
            ->setDateGenerate(new \DateTime())
            ->setEvent($this->event)
            ->setData([
                'show'      =>  $showData,
                'confirm'   =>  false//не подтверждено
            ]);

        $this->em->persist($executeFlatEvent);
        $this->em->flush();

        //добавляем лог - сгенерировалось событие "Подача искового заявления"
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

        return true;
    }
}