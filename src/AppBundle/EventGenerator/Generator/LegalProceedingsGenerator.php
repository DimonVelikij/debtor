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
        return 'legal_proceedings';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        if ($flatEvent->getParameter('deferred')) {
            //если заседаные было отложено - выполняем текущее событие в дату на которое отложено заседание
            /** @var \DateTime $dateMeeting */
            $dateMeeting = $flatEvent->getParameter('dateMeeting');

            return $this->dateDiffer->getDays(new \DateTime(), $dateMeeting);
        } elseif ($flatEvent->getParameter('confirm') || $flatEvent->getParameter('miss')) {
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
        if ($flatEvent->getParameter('deferred')) {
            //если заседание было отложено - возвращаем текущий генератор
            return [$this];
        } elseif ($flatEvent->getParameter('confirm') || $flatEvent->getParameter('miss')) {
            //если принято решение или пропущено - возвращаем генератор "заявление на получение исполнительного листа"
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
        /** @var FlatEvent|null $currentFlatEvent */
        $currentFlatEvent = $this->getFlatEvent((int)$request->get('flat_id'));

        if (
            !$currentFlatEvent ||
            $currentFlatEvent->getParameter('deferred') ||
            $currentFlatEvent->getParameter('confirm')
        ) {
            //действие выполнено
            return true;
        }

        if ($request->get('action') == 'confirm') {
            //записываем в event_data дату принятия решения
            $this->em->persist($currentFlatEvent->getFlat()->setEventDataParameter('legal_proceedings', [
                'confirm'   =>  new \DateTime()
            ]));

            $showData = "Принято решение";

            $currentFlatEvent
                ->setDateGenerate(new \DateTime())
                ->setData([
                    'show'      =>  $showData,
                    'confirm'   =>  true
                ]);

            $this->em->persist($currentFlatEvent);
            $this->em->flush();

            //добавляем лог - принято решение
            $this->flatLogger->log($currentFlatEvent->getFlat(), "<b>{$this->event->getName()}</b><br>{$showData}", $this->event);

            return true;
        } else {
            //заседание отложено
            $data = json_decode($request->getContent(), true);

            //входные данные
            $input = [
                'dateMeeting'   =>  $data['dateMeeting'] ?? null,
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
                Дата заседания: " . substr($input['dateMeeting'], 0, 2) . "." . substr($input['dateMeeting'], 2, 2) . "." . substr($input['dateMeeting'], 4, 4) . "<br>
                Время заседания: " . substr($input['timeMeeting'], 0, 2) . ":" . substr($input['timeMeeting'], 2, 2);

            $currentFlatEvent
                ->setDateGenerate(new \DateTime())
                ->setData([
                    'deferred'      =>  true,
                    'dateMeeting'   =>  \DateTime::createFromFormat('dmY', $input['dateMeeting']),
                    'timeMeeting'   =>  $input['timeMeeting'],
                    'show'          =>  $showData
                ]);

            //записываем в event_data дату получения определения и данные следующего заседания
            $this->em->persist($currentFlatEvent->getFlat()->setEventDataParameter('legal_proceedings', [
                'deferred'      =>  new \DateTime(),
                'date_meeting'  =>  \DateTime::createFromFormat('dmY', $input['dateMeeting']),
                'time_meeting'  =>  $input['timeMeeting']
            ]));

            $this->em->persist($currentFlatEvent);
            $this->em->flush();

            //добавляем лог - заседание отложено
            $this->flatLogger->log($currentFlatEvent->getFlat(), "<b>{$this->event->getName()}</b><br>{$showData}", $this->event);

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
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}", $this->event);

        return true;
    }

    /**
     * @param FlatEvent $flatEvent
     * @return \AppBundle\Entity\Event|null|object
     */
    public function getNextEvent(FlatEvent $flatEvent)
    {
        if ($flatEvent->getParameter('deferred')) {
            return $this->event;
        } elseif ($flatEvent->getParameter('confirm') || $flatEvent->getParameter('miss')) {
            return $this->em->getRepository('AppBundle:Event')->findOneBy(['alias' => 'statement_receipt_writ_execution']);
        } else {
            return null;
        }
    }
}
