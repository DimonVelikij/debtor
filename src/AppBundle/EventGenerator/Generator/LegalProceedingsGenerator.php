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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LegalProceedingsGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * LegalProceedingsGenerator constructor.
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
        return 'legal_proceedings';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        if ($flatEvent->getParameter('deferred', false)) {
            //если заседаные было отложено - выполняем текущее событие в дату на которое отложено заседание
            /** @var \DateTime $dateMeeting */
            $dateMeeting = $flatEvent->getParameter('dateMeeting', false);

            return $dateMeeting->diff(new \DateTime())->d;
        } elseif ($flatEvent->getParameter('confirm', false)) {
            //если решение принято - через 30 дней заявление на получение исполнительного листа
            return 30;
        } else {
            //если пользователь ничего не выбрал - ничего не делаем
            return INF;
        }
    }

    /**
     * @param FlatEvent $flatEvent
     * @return array
     */
    public function getNextEventGenerators(FlatEvent $flatEvent)
    {
        if ($flatEvent->getParameter('deferred', false)) {
            //если заседание было отложено - возвращаем текущий генератор
            return [$this];
        } elseif ($flatEvent->getParameter('confirm', false)) {
            //если принято решение - возвращаем генератор "заявление на получение исполнительного листа"
            $generatorAlias = 'statement_receipt_writ_execution';
        } else {
            //пользователь ничего не делал - ничего не возвращаем
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
     * @return bool|array
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

        if (
            !$currentFlatEvent ||
            $flatEvent->getParameter('deferred') ||
            $flatEvent->getParameter('confirm')
        ) {
            //действие выполнено
            return true;
        }

        if ($request->get('action') == 'confirm') {
            $showData = "Принято решение";
            $flatEvent
                ->setData([
                    'show'      =>  $showData,
                    'confirm'   =>  true
                ]);

            $this->em->persist($flatEvent);
            $this->em->flush();

            //добавляем лог - принято решение
            $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

            return true;
        } else {
            //заседание отложено
            $data = json_decode($request->getContent(), true);

            //входные данные
            $input = [
                'dateMeeting'   => $data['dateMeeting'] ?? null,
                'timeMeeting'   =>  $data['timeMeeting'] ?? null
            ];

            $constraints = [
                'dateMeeting'       =>  [
                    new NotBlank(['message' =>  'Укажите дату заседания']),
                    new Regex(['pattern'    => '/^([0-2]\d|3[01])(0\d|1[012])(19|20)(\d\d)$/', 'message' => 'Неверно указана дата заседания'])
                ],
                'timeMeeting'       =>  [
                    new NotBlank(['message' =>  'Укажите время заседания']),
                    new Regex(['pattern'    =>  '/^([0,1]\d|2[0-3])([0-4]\d|5[0-9])$/', 'message' => 'Неверно указано время заседания'])
                ]
            ];

            $errors = [];

            foreach ($constraints as $name => $constraint) {
                $validationResult = $this->validator->validate($input[$name], $constraint);

                if (count($validationResult)) {
                    $errors[$name] = $this->translator->trans($validationResult[0]->getMessage());
                }
            }

            if (count($errors)) {
                return [
                    'success'   =>  false,
                    'errors'    =>  $errors
                ];
            }

            $showData = "
                Заседание отложено<br>
                Дата заседания: " . substr($input['dateMeeting'], 0, 2) . ":" . substr($input['dateMeeting'], 2, 2) . ":" . substr($input['dateMeeting'], 4, 4) . "<br>
                Время заседания: " . substr($input['timeMeeting'], 0, 2) . ":" . substr($input['timeMeeting'], 2, 2);

            $currentFlatEvent
                ->setData([
                    'deferred'      =>  true,
                    'dateMeeting'   =>  \DateTime::createFromFormat('dmY', $input['dateMeeting']),
                    'timeMeeting'   =>  $input['timeMeeting'],
                    'show'          =>  $showData
                ]);

            $this->em->persist($currentFlatEvent);
            $this->em->flush();

            //добавляем лог - заседание отложено
            $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

            return [
                'success'   =>  true,
                'errors'    =>  false
            ];
        }
    }

    /**
     * @param FlatEvent $flatEvent
     * @return bool
     */
    public function generateEvent(FlatEvent $flatEvent)
    {
        /** @var Flat $flat */
        $flat = $flatEvent->getFlat();

        $showData = $this->twig->render('@App/Admin/Flat/EventLayer/legal_proceedings_layer.html.twig', [
            'flat'  =>  $flat,
            'event' =>  $this->event
        ]);

        $this->em->remove($flatEvent);
        $this->em->flush();

        //добавляем событие "Судебное делопроизводство"
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

        //добавляем лог - сгенерировалось событие "Судебное делопроизводство"
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

        return true;
    }

    public function getNextEvent(FlatEvent $flatEvent)
    {
        // TODO: Implement getNextEvent() method.
    }
}
