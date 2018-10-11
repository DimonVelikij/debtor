<?php

namespace AppBundle\EventGenerator\Generator;

use AppBundle\Entity\Event;
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

class Pretense2Generator extends BaseGenerator implements GeneratorInterface
{
    /**
     * Pretense2Generator constructor.
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
        return 'pretense2';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        //если уже выполнялись события после "Претензии 2" - то повторное выполнение через 30 дней
        //иначе выполняем следующее событие после "Претензия 2"
        return $flatEvent->getFlat()->getFlatsEvents()->count() > 1 ? 30 : 1;
    }

    /**
     * @param FlatEvent $flatEvent
     * @return array
     */
    public function getNextEventGenerators(FlatEvent $flatEvent)
    {
        //если уже выполнялись события после "Претензия 2" - то вызвращаем текущий объект для повторной генерации "Претензия2"
        return $flatEvent->getFlat()->getFlatsEvents()->count() > 1 ?
            [$this] :
            $this->nextEventGenerators;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function processUserAction(Request $request)
    {
        //у текущего события отсутствует обработка действий от пользователя
        return true;
    }

    /**
     * @param FlatEvent $flatEvent
     * @return bool
     */
    public function generateEvent(FlatEvent $flatEvent)
    {
        $this->validateEvent();

        /** @var Flat $flat */
        $flat = $flatEvent->getFlat();

        /** @var array $documentLinks */
        $documentLinks = $this->templateGenerator->generateTemplate($flat, $this->event);

        $showData = '';

        /** @var string $documentLink */
        foreach ($documentLinks as $documentLink) {
            $showData .= "<a href='{$documentLink}' target='_blank'>Посмотреть</a> <a href='{$documentLink}' target='_blank' download>Скачать</a><br>";
        }

        //если текущее событие "Претензия1"
        if ($flatEvent->getEvent()->getAlias() == 'pretense1') {
            //удаляем событие "Претензия1"
            $this->em->remove($flatEvent);

            //добавляем событие "Претензия2"
            $executeFlatEvent = new FlatEvent();
            $executeFlatEvent
                ->setFlat($flat)
                ->setDateGenerate(new \DateTime())
                ->setEvent($this->event)
                ->setData([
                    'show'  =>  $showData
                ]);
        } else {
            //обновляем событие "Претензия2"
            $executeFlatEvent = $flatEvent;
            $executeFlatEvent
                ->setDateGenerate(new \DateTime())
                ->setData([
                    'show'  =>  $showData
                ]);
        }

        $this->em->persist($executeFlatEvent);
        $this->em->flush();

        //добавляем лог - сгенерировалось событие "Претензия2"
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}", $this->event);

        return true;
    }

    /**
     * @param FlatEvent $flatEvent
     * @return \AppBundle\Entity\Event|null|object
     */
    public function getNextEvent(FlatEvent $flatEvent)
    {
        if ($flatEvent->getFlat()->getFlatsEvents()->count() > 1) {
            //если "Претензия2" и еще другое событие уже были сформированы, то следующее событие "Претензия2"
            return $this->event;
        } else {
            //если была сформирована только "Претензия2" - следующее событие "Формирование заявления на выдачу судебного приказа"
            return $this->em->getRepository('AppBundle:Event')->findOneBy(['alias' => 'formation_court_order']);
        }
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function miss(Request $request)
    {
        /** @var FlatEvent|null $flatEvent */
        $flatEvent = $this->getFlatEvent($request->get('flat_id'));

        if (!$flatEvent) {
            return false;
        }

        /** @var Event|null $nextEvent */
        $nextEvent = $this->getNextEvent($flatEvent);

        if (!$nextEvent) {
            return false;
        }

        if ($flatEvent->getFlat()->getFlatsEvents()->count() > 1) {
            //если "Претензия2" уже формировалась - делаем пропуск у события "Претензия2" ничего не добавляя
            $flatEvent
                ->setDateGenerate(new \DateTime())
                ->setData($this->getMissData());

            $this->em->persist($flatEvent);
        } else {
            //если "Претензия2" еще не формировалась
            $missFlatEvent = new FlatEvent();
            $missFlatEvent
                ->setFlat($flatEvent->getFlat())
                ->setDateGenerate(new \DateTime())
                ->setEvent($nextEvent)
                ->setData($this->getMissData());

            $this->em->persist($missFlatEvent);
        }

        $this->em->flush();

        //добавляем лог - пропущено событие
        $this->flatLogger->log($flatEvent->getFlat(), "<b>{$nextEvent->getName()}</b><br>Пропущено", $this->event);

        return true;
    }
}