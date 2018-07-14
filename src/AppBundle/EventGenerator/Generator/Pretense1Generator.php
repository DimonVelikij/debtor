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

class Pretense1Generator extends BaseGenerator implements GeneratorInterface
{
    /**
     * Pretense1Generator constructor.
     * @param EntityManager $em
     * @param FlatLogger $flatLogger
     * @param Router $router
     * @param TemplateGenerator $templateGenerator
     */
    public function __construct(EntityManager $em, FlatLogger $flatLogger, Router $router, TemplateGenerator $templateGenerator)
    {
        parent::__construct($em, $flatLogger, $router, $templateGenerator);
    }

    /**
     * @return string
     */
    public function getEventAlias()
    {
        return 'pretense1';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        return 30;
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
     * @param Flat $flat
     * @param FlatEvent $flatEvent
     * @return bool
     */
    public function eventGenerate(Flat $flat, FlatEvent $flatEvent)
    {
        $this->validateEvent();

        /** @var array $documentLinks */
        $documentLinks = $this->templateGenerator->generateTemplate($flat, $this->event);

        $showData = '';

        /** @var string $documentLink */
        foreach ($documentLinks as $documentLink) {
            $showData .= "<a href='{$documentLink}' target='_blank'>Посмотреть</a> <a href='{$documentLink}' target='_blank' download>Скачать</a><br>";
        }

        //удаляем событие "Должник поступил в работу"
        $this->em->remove($flatEvent);

        //добавляем событие "Претензия1"
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

        //добавляем лог - сгенерировалось событие "Претензия1"
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

        return true;
    }
}