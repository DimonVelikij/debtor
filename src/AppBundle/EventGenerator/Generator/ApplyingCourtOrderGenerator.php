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

class ApplyingCourtOrderGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * ApplyingCourtOrderGenerator constructor.
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
        return 'applying_court_order';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        //если подача заявления подтверждена или пропущена, то можно приступать к следующему событию, иначе нельзя
        return $flatEvent->getParameter('confirm') || $flatEvent->getParameter('miss') ? 40 : INF;
    }

    /**
     * @param FlatEvent $flatEvent
     * @return array
     */
    public function getNextEventGenerators(FlatEvent $flatEvent)
    {
        //если подача заявления подтверждена, то отдаем следующие генераторы, иначе - пустой массив
        return $flatEvent->getParameter('confirm') || $flatEvent->getParameter('miss') ?
            $this->nextEventGenerators :
            [];
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
            $currentFlatEvent->getParameter('confirm')
        ) {
            //уже подтверждено
            return true;
        }

        $currentData = new \DateTime();

        //записываем в event_data - дату подачи заявления судебного приказа в суд
        $this->em->persist($currentFlatEvent->getFlat()->setEventDataParameter('applying_court_order', [
            'confirm'   =>  $currentData
        ]));

        $showData = "Подтверждено {$currentData->format('d.m.Y')}";

        $currentFlatEvent
            ->setDateGenerate($currentData)
            ->setData([
                'show'      =>  $showData,
                'confirm'   =>  true//подтверждено - можно через 40 дней выполнять следующее событие
            ]);

        $this->em->persist($currentFlatEvent);
        $this->em->flush();

        //добавляем лог - что все подтверждения подача заявления на СП в суд
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

        $showData = "<a href='{$this->router->generate('admin_app_flat_process_user', ['event' => $this->event->getAlias(), 'flat_id' => $flat->getId()])}'>Подтвердить подачу заявления судебного приказа в суд</a>";

        //удаляем событие "Формирование заявления на выдачу судебного приказа"
        $this->em->remove($flatEvent);

        //добавляем событие "Подача заявления на выдачу судебного приказа"
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

        //добавляем лог - сгенерировалось событие "Подача заявления на выдачу судебного приказа"
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

        return true;
    }

    /**
     * @param FlatEvent $flatEvent
     * @return \AppBundle\Entity\Event|null|object
     */
    public function getNextEvent(FlatEvent $flatEvent)
    {
        return $flatEvent->getParameter('confirm') || $flatEvent->getParameter('miss') ?
            $this->em->getRepository('AppBundle:Event')->findOneBy(['alias' => 'obtaining_court_order']) :
            null;
    }
}