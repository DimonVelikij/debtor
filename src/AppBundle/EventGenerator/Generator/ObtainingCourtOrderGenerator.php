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

class ObtainingCourtOrderGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * @var GeneratorInterface
     */
    private $formationCourtOrderGenerator;

    /**
     * ObtainingCourtOrderGenerator constructor.
     * @param EntityManager $em
     * @param FlatLogger $flatLogger
     * @param Router $router
     * @param TemplateGenerator $templateGenerator
     * @param TwigEngine $templating
     * @param GeneratorInterface $formationCourtOrderGenerator
     */
    public function __construct(EntityManager $em, FlatLogger $flatLogger, Router $router, TemplateGenerator $templateGenerator, TwigEngine $templating, GeneratorInterface $formationCourtOrderGenerator)
    {
        parent::__construct($em, $flatLogger, $router, $templateGenerator, $templating);
        $this->formationCourtOrderGenerator = $formationCourtOrderGenerator;
    }

    /**
     * @return string
     */
    public function getEventAlias()
    {
        return 'obtaining_court_order';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        //если пользователь не подтвердил подтверждение или отказ или отмену - дальше ничего нельзя генерировать
        return
            !$flatEvent->getParameter('confirm', false) &&
            !$flatEvent->getParameter('failure', false) &&
            !$flatEvent->getParameter('cancel', false) ? INF : 1;
    }

    public function getNextEventGenerators(FlatEvent $flatEvent)
    {
        
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

        $currentDate = new \DateTime();
        $actions = [
            'confirm'   =>  'Подтверждено',
            'failure'   =>  'Отказано',
            'cancel'    =>  'Отменено'
        ];

        $showData = "{$actions[$request->get('action')]} {$currentDate->format('d.m.Y')}";

        //добавляем лог - либо подтверждение, либо отказ, либо отмена судебного приказа
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

        switch ($request->get('action')) {
            case 'confirm':
                $currentFlatEvent
                    ->setParameter('show', $showData)
                    ->setParameter('confirm', true);

                $this->em->persist($currentFlatEvent);
                $this->em->flush();

                break;
            case 'failure':
                //при отказе заново выполняется событие "Формирование заявления на выдачу судебного приказа"
                //текущее событите будет удалено
                $this->formationCourtOrderGenerator->eventGenerate($flat, $flatEvent);
                break;
            case 'cancel':
                $currentFlatEvent
                    ->setParameter('show', $showData)
                    ->setParameter('cancel', true);

                $this->em->persist($currentFlatEvent);
                $this->em->flush();

                break;
        }

        return true;
    }

    /**
     * @param Flat $flat
     * @param FlatEvent $flatEvent
     * @return bool
     */
    public function eventGenerate(Flat $flat, FlatEvent $flatEvent)
    {
        $showData = "
            <a href='{$this->router->generate('admin_app_flat_process_user', ['event' => $this->event->getAlias(), 'flat_id' => $flat->getId(), 'action' => 'confirm'])}'>Подтвердить получение</a><br>
            <a href='{$this->router->generate('admin_app_flat_process_user', ['event' => $this->event->getAlias(), 'flat_id' => $flat->getId(), 'action' => 'failure'])}'>Отказ в принятии</a><br>
            <a href='{$this->router->generate('admin_app_flat_process_user', ['event' => $this->event->getAlias(), 'flat_id' => $flat->getId(), 'action' => 'cancel'])}'>Отмена судебного приказа</a><br>
            ";

        //удаляем событие "Подача заявления на выдачу судебного приказа"
        $this->em->remove($flatEvent);

        //добавляем событие "Получение судебного приказа"
        $executeFlatEvent = new FlatEvent();
        $executeFlatEvent
            ->setFlat($flat)
            ->setDateGenerate(new \DateTime())
            ->setEvent($this->event)
            ->setData([
                'show'      =>  $showData,
                'confirm'   =>  false,//не подтверждено
                'failure'   =>  false,//не отказано
                'cancel'    =>  false//не отменено
            ]);

        $this->em->persist($executeFlatEvent);
        $this->em->flush();

        //добавляем лог - сгенерировалось событие "Получение судебного приказа"
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

        return true;
    }
}