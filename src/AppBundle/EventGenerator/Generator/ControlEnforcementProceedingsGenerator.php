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
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ControlEnforcementProceedingsGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * ControlEnforcementProceedingsGenerator constructor.
     * @param EntityManager $em
     * @param FlatLogger $flatLogger
     * @param Router $router
     * @param TemplateGenerator $templateGenerator
     * @param TwigEngine $twig
     * @param ValidatorInterface $validator
     * @param DataCollectorTranslator $translator
     */
    public function __construct(EntityManager $em, FlatLogger $flatLogger, Router $router, TemplateGenerator $templateGenerator, TwigEngine $twig, ValidatorInterface $validator, DataCollectorTranslator $translator)
    {
        parent::__construct($em, $flatLogger, $router, $templateGenerator, $twig, $validator, $translator);
    }

    /**
     * @return string
     */
    public function getEventAlias()
    {
        return 'control_enforcement_proceedings';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        if ($flatEvent->getParameter('process')) {
            //если в процессе - через 1 день
            return 1;
        } elseif ($flatEvent->getParameter('inactivity')) {
            //если бездействие ФССП - через 1 день
            return 1;
        } else {
            //пользователь ничего не делал
            return INF;
        }
    }

    /**
     * @param FlatEvent $flatEvent
     * @return array
     */
    public function getNextEventGenerators(FlatEvent $flatEvent)
    {
        if ($flatEvent->getParameter('process')) {
            return [$this];
        } elseif ($flatEvent->getParameter('inactivity')) {
            $generatorAlias = 'statement_preparation_fssp';
        } else {
            $generatorAlias = false;
        }

        //ищем по алиасу нужный генератор
        $nextEventGenerators = [];

        /** @var GeneratorInterface $nextEventGenerator */
        foreach ($this->nextEventGenerators as $nextEventGenerator) {
            if ($nextEventGenerator->getEventAlias() == $generatorAlias) {
                $nextEventGenerators[] = $nextEventGenerator;
                break;
            }
        }

        return $nextEventGenerators;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function processUserAction(Request $request)
    {
        /** @var FlatEvent|null $currentFlatEvent */
        $currentFlatEvent = $this->getFlatEvent((int)$request->get('flat_id'));

        if (
            !$currentFlatEvent ||
            $currentFlatEvent->getParameter('inactivity') ||
            $currentFlatEvent->getParameter('finish')
        ) {
            //действие уже выполнено
            return true;
        }

        $currentDate = new \DateTime();
        $action = [
            'process'       =>  'Исполнительное производство проводится',
            'inactivity'    =>  'Бездействие ФССП',
            'finish'        =>  'Исполнительное производство окончено'
        ];

        $showData = "{$action[$request->get('action')]} {$currentDate->format('d.m.Y')}";

        switch ($request->get('action')) {
            case 'process':
                $currentFlatEvent
                    ->setDateGenerate($currentDate)
                    ->setData([
                        'show'      =>  $showData,
                        'process'   =>  true
                    ]);

                    $this->em->persist($currentFlatEvent);
                    $this->em->flush();
                break;
            case 'inactivity':
                $currentFlatEvent
                    ->setDateGenerate($currentDate)
                    ->setData([
                        'show'          =>  $showData,
                        'inactivity'    =>  true
                    ]);

                $this->em->persist($currentFlatEvent);
                $this->em->flush();
                break;
            case 'finish':
                //работа с должником прекращается
                foreach ($currentFlatEvent->getFlat()->getFlatsEvents() as $flatEvent) {
                    $this->em->remove($flatEvent);
                }
                $this->em->flush();
                break;
        }

        //добавляем лог - либо проводится, либо бездействие, либо окончено
        $this->flatLogger->log($currentFlatEvent->getFlat(), "<b>{$this->event->getName()}</b><br>{$showData}");

        return true;
    }

    /**
     * @param FlatEvent $flatEvent
     * @return bool
     */
    public function generateEvent(FlatEvent $flatEvent)
    {
        /** @var Flat $flat */
        $flat = $flatEvent->getFlat();

        $showData = "
            <a href='{$this->router->generate('admin_app_flat_process_user', ['event' => $this->event->getAlias(), 'flat_id' => $flat->getId(), 'action' => 'process'])}'>Исполнительное производство проводится</a><br>
            <a href='{$this->router->generate('admin_app_flat_process_user', ['event' => $this->event->getAlias(), 'flat_id' => $flat->getId(), 'action' => 'inactivity'])}'>Бездействие ФССП</a><br>
            <a href='{$this->router->generate('admin_app_flat_process_user', ['event' => $this->event->getAlias(), 'flat_id' => $flat->getId(), 'action' => 'finish'])}'>Исполнительное производство окончено</a><br>
            ";

        //удаляем событие "Подача заявления на возбуждение исполнительного производства" или "Контроль исполнительного производства"
        $this->em->remove($flatEvent);
        $this->em->flush();

        //добавляем событие "Контроль исполнительного производства"
        $executeFlatEvent = new FlatEvent();
        $executeFlatEvent
            ->setFlat($flat)
            ->setDateGenerate(new \DateTime())
            ->setEvent($this->event)
            ->setData([
                'show'  =>  $showData
            ]);

        $this->em->persist($executeFlatEvent);
        $this->em->flush();

        //добавляем лог - сгенерировалось событие "Контроль исполнительного производства"
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

        return true;
    }

    /**
     * @param FlatEvent $flatEvent
     * @return \AppBundle\Entity\Event|null|object
     */
    public function getNextEvent(FlatEvent $flatEvent)
    {
        if ($flatEvent->getParameter('process')) {
            return $this->event;
        } elseif ($flatEvent->getParameter('inactivity')) {
            return $this->em->getRepository('AppBundle:Event')->findOneBy(['alias' => 'statement_preparation_fssp']);
        } else {
            return null;
        }
    }
}