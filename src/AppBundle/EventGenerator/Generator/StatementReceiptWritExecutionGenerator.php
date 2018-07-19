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

class StatementReceiptWritExecutionGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * StatementReceiptWritExecutionGenerator constructor.
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
        return 'statement_receipt_writ_execution';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        return 0;
    }

    /**
     * @param FlatEvent $flatEvent
     * @return array
     */
    public function getNextEventGenerators(FlatEvent $flatEvent)
    {
        return [];
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function processUserAction(Request $request)
    {
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

        $documentLinks = [];

        //если не было никаких действий по этому событию - генерируем документ
        if (
            !$flatEvent->getParameter('appealed', false) &&
            !$flatEvent->getParameter('not_appealed', false)
        ) {
            /** @var array $documentLinks */
            $documentLinks = $this->templateGenerator->generateTemplate($flat, $this->event);
            //удаление
        }

        $showData = $this->twig->render('@App/Admin/Flat/EventLayer/statement_receipt_writ_execution_layer.html.twig', [
            'flat'              =>  $flat,
            'event'             =>  $this->event,
            'document_links'    =>  $documentLinks
        ]);

        return true;
    }
}