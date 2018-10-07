<?php

namespace AppBundle\EventGenerator\Generator;

use AppBundle\Entity\Flat;
use AppBundle\Entity\FlatEvent;
use AppBundle\Service\DateDiffer;
use AppBundle\Service\FlatLogger;
use AppBundle\Service\TemplateGenerator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ObtainingCourtOrderGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * ObtainingCourtOrderGenerator constructor.
     * @param EntityManager $em
     * @param FlatLogger $flatLogger
     * @param Router $router
     * @param TemplateGenerator $templateGenerator
     * @param DateDiffer $dateDiffer
     * @param TwigEngine $twig
     * @param ValidatorInterface $validator
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManager $em, FlatLogger $flatLogger, Router $router, TemplateGenerator $templateGenerator, DateDiffer $dateDiffer, TwigEngine $twig, ValidatorInterface $validator, TranslatorInterface $translator)
    {
        parent::__construct($em, $flatLogger, $router, $templateGenerator, $dateDiffer, $twig, $validator, $translator);
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
        if (
            $flatEvent->getParameter('confirm') ||
            $flatEvent->getParameter('cancel') ||
            $flatEvent->getParameter('miss')
        ) {
            //если пользователь подтвердил или отменил получение приказа - выполняем следующее событие через 1 день
            return 1;
        } elseif ($flatEvent->getParameter('failure')) {
            //если пользователю отказано - выполняем следующее событие сразу при следующей генерации
            return 0;
        } else {
            //если пользователь ничего не делел - следующее событие не генерируем
            return INF;
        }
    }

    /**
     * @param FlatEvent $flatEvent
     * @return array
     */
    public function getNextEventGenerators(FlatEvent $flatEvent)
    {
        if ($flatEvent->getParameter('confirm')) {
            //если подтверждено получение приказа - следующее событие "Заявление на возбуждение исполнительного производства"
            $generatorAlias = 'statement_commencement_enforcement_proceedings';
        } elseif ($flatEvent->getParameter('failure')) {
            //если отказано получение приказа - следующее событие "Формирование заявления на выдачу судебного приказа"
            $generatorAlias = 'formation_court_order';
        } elseif ($flatEvent->getParameter('cancel') || $flatEvent->getParameter('miss')) {
            //если отменено получение приказа - следующее событие "Формирование искового заявления"
            $generatorAlias = 'formation_statement_claim';
        } else {
            //если пользователь ничего не делал - не отдаем генератор
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

        if(
            !$currentFlatEvent ||
            $currentFlatEvent->getParameter('confirm') ||
            $currentFlatEvent->getParameter('failure') ||
            $currentFlatEvent->getParameter('cancel')
        ) {
            //действие уже выполнено
            return true;
        }

        $currentDate = new \DateTime();
        $actions = [
            'confirm'   =>  'Подтверждено получение судебного приказа на руки',
            'failure'   =>  'Подтверждено получение определения об отказе в принятии',
            'cancel'    =>  'Получено определение об отмене судебного приказа'
        ];

        $showData = "{$actions[$request->get('action')]} {$currentDate->format('d.m.Y')}";

        switch ($request->get('action')) {
            case 'confirm':
                $currentFlatEvent
                    ->setDateGenerate($currentDate)
                    ->setParameter('show', $showData)
                    ->setParameter('confirm', true);

                //записываем в event_data - дата получения судебного приказа
                $this->em->persist($currentFlatEvent->getFlat()->setEventDataParameter(
                    'obtaining_court_order', [
                        'confirm'   =>  $currentDate
                    ]
                ));

                break;
            case 'failure':
                $currentFlatEvent
                    ->setDateGenerate($currentDate)
                    ->setParameter('show', $showData)
                    ->setParameter('failure', true);

                //записываем в event_data дату получения определения об отказе в принятии
                $this->em->persist($currentFlatEvent->getFlat()->setEventDataParameter(
                    'obtaining_court_order', [
                        'failure'   =>  $currentDate
                    ]
                ));

                break;
            case 'cancel':
                $currentFlatEvent
                    ->setDateGenerate($currentDate)
                    ->setParameter('show', $showData)
                    ->setParameter('cancel', true);

                //записываем в event_data дату получения определения об отмене судебного приказа
                $this->em->persist($currentFlatEvent->getFlat()->setEventDataParameter(
                    'obtaining_court_order', [
                        'cancel'    =>  $currentDate
                    ]
                ));

                break;
        }

        $this->em->persist($currentFlatEvent);
        $this->em->flush();

        //добавляем лог - либо подтверждение, либо отказ, либо отмена судебного приказа
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
            <a href='{$this->router->generate('admin_app_flat_process_user', ['event' => $this->event->getAlias(), 'flat_id' => $flat->getId(), 'action' => 'confirm'])}'>Подтвердить получение судебного приказа на руки</a><br>
            <a href='{$this->router->generate('admin_app_flat_process_user', ['event' => $this->event->getAlias(), 'flat_id' => $flat->getId(), 'action' => 'failure'])}'>Подтвердить получения определения об отказе  принятии</a><br>
            <a href='{$this->router->generate('admin_app_flat_process_user', ['event' => $this->event->getAlias(), 'flat_id' => $flat->getId(), 'action' => 'cancel'])}'>Получить определение об отмене судебного приказа</a><br>
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
                'show'      =>  $showData
            ]);

        $this->em->persist($executeFlatEvent);
        $this->em->flush();

        //добавляем лог - сгенерировалось событие "Получение судебного приказа"
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

        return true;
    }

    /**
     * @param FlatEvent $flatEvent
     * @return \AppBundle\Entity\Event|null|object
     */
    public function getNextEvent(FlatEvent $flatEvent)
    {
        $eventRepository = $this->em->getRepository('AppBundle:Event');
        if ($flatEvent->getParameter('failure')) {
            return $eventRepository->findOneBy(['alias' => 'formation_court_order']);
        } elseif ($flatEvent->getParameter('cancel') || $flatEvent->getParameter('miss')) {
            return $eventRepository->findOneBy(['alias' =>  'formation_statement_claim']);
        } elseif ($flatEvent->getParameter('confirm')) {
            return $eventRepository->findOneBy(['alias'  =>  'statement_commencement_enforcement_proceedings']);
        } else {
            return null;
        }
    }
}