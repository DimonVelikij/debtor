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

class ObtainingCourtOrderGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * ObtainingCourtOrderGenerator constructor.
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
        return 'obtaining_court_order';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        if ($flatEvent->getParameter('confirm', false) || $flatEvent->getParameter('cancel', false)) {
            //если пользователь подтвердил или отменил получение приказа - выполняем следующее событие через 1 день
            return 1;
        } elseif ($flatEvent->getParameter('failure', false)) {
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
        if ($flatEvent->getParameter('confirm', false)) {
            //если подтверждено получение приказа - следующее событие "Заявление на возбуждение исполнительного производства"
            $generatorAlias = 'statement_commencement_enforcement_proceedings';
        } elseif ($flatEvent->getParameter('failure', false)) {
            //если отказано получение приказа - следующее событие "Формирование заявления на выдачу судебного приказа"
            $generatorAlias = 'formation_court_order';
        } elseif ($flatEvent->getParameter('cancel', false)) {
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

        if(
            !$currentFlatEvent ||
            $currentFlatEvent->getParameter('confirm', false) ||
            $currentFlatEvent->getParameter('failure', false) ||
            $currentFlatEvent->getParameter('cancel', false)
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
                    ->setParameter('show', $showData)
                    ->setParameter('confirm', true);

                break;
            case 'failure':
                $currentFlatEvent
                    ->setParameter('show', $showData)
                    ->setParameter('failure', true);

                break;
            case 'cancel':
                $currentFlatEvent
                    ->setParameter('show', $showData)
                    ->setParameter('cancel', true);

                break;
        }

        $this->em->persist($currentFlatEvent);
        $this->em->flush();

        //добавляем лог - либо подтверждение, либо отказ, либо отмена судебного приказа
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

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