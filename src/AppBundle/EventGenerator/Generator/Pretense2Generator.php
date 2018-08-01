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

class Pretense2Generator extends BaseGenerator implements GeneratorInterface
{
    /**
     * Pretense2Generator constructor.
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
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

        return true;
    }

    public function getNextEvent(FlatEvent $flatEvent)
    {
        // TODO: Implement getNextEvent() method.
    }
}