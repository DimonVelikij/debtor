<?php

namespace AppBundle\EventGenerator\Generator;

use AppBundle\Entity\Flat;
use AppBundle\Entity\FlatEvent;
use AppBundle\Service\FlatLogger;
use AppBundle\Service\TemplateGenerator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;

class FormationCourtOrderGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * FormationCourtOrderGenerator constructor.
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
    protected function getEventAlias()
    {
        return 'formation_court_order';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        return 3;
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

        //удалять предыдущее событие "Претензия 2" не нужно, т.к. оно должно выполняться пока не погасится долг
        //добавляем событие "Формирование заявления на выдачу судебного приказа"
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

        //добавляем лог - сгенерировалось событие "Формирование заявления на выдачу судебного приказа"
        $this->flatLogger->log($flat, $this->event->getName() . "<br>" . $showData);

        return true;
    }
}