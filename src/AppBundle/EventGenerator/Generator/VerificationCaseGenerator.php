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
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VerificationCaseGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * VerificationCaseGenerator constructor.
     * @param EntityManager $em
     * @param FlatLogger $flatLogger
     * @param Router $router
     * @param TemplateGenerator $templateGenerator
     * @param DateDiffer $dateDiffer
     * @param TwigEngine $twig
     * @param ValidatorInterface $validator
     * @param DataCollectorTranslator $translator
     */
    public function __construct(EntityManager $em, FlatLogger $flatLogger, Router $router, TemplateGenerator $templateGenerator, DateDiffer $dateDiffer, TwigEngine $twig, ValidatorInterface $validator, DataCollectorTranslator $translator)
    {
        parent::__construct($em, $flatLogger, $router, $templateGenerator, $dateDiffer, $twig, $validator, $translator);
    }

    /**
     * @return string
     */
    public function getEventAlias()
    {
        return 'verification_case';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        if ($flatEvent->getParameter('failure')) {
            //если пользователь выбрал отказ - генерируем следующее событие при следующем выполнении таски
            return 0;
        } elseif ($flatEvent->getParameter('confirm')) {
            //если пользователь выбрал принятие - вычисляем разницу между текущим временем и датой заседания
            /** @var \DateTime $dateMeeting */
            $dateMeeting = $flatEvent->getParameter('dateMeeting');

            return $this->dateDiffer->getDays(new \DateTime(), $dateMeeting);
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
        if ($flatEvent->getParameter('failure')) {
            $generatorAlias = 'formation_statement_claim';
        } elseif ($flatEvent->getParameter('confirm')) {
            $generatorAlias = 'legal_proceedings';
        } else {
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
            $currentFlatEvent->getParameter('confirm') ||
            $currentFlatEvent->getParameter('failure')
        ) {
            //действие выполнено
            return true;
        }

        //если отказ
        if ($request->get('action') == 'failure') {
            $showData = "Отказ в принятии искового заявления к производству";
            $currentFlatEvent
                ->setDateGenerate(new \DateTime())
                ->setData([
                    'show'      =>  $showData,
                    'failure'   =>  true
                ]);

            $this->em->persist($currentFlatEvent);
            $this->em->flush();

            //добавляем лог - отказ принятия искового заявления
            $this->flatLogger->log($currentFlatEvent->getFlat(), "<b>{$this->event->getName()}</b><br>{$showData}");

            return true;
        } else {//если принятие
            $data = json_decode($request->getContent(), true);

            //входные данные с фронта
            $input = [
                'courtCaseNumber'   =>  $data['courtCaseNumber'] ?? null,
                'judge'             =>  $data['judge'] ?? null,
                'dateMeeting'       =>  $data['dateMeeting'] ?? null,
                'timeMeeting'       =>  $data['timeMeeting'] ?? null
            ];

            $constraints = [
                'courtCaseNumber'   =>  [
                    new NotBlank(['message' => 'Укажите номер дела'])
                ],
                'judge'             =>  [
                    new NotBlank(['message' =>  'Укажите информацию о судье'])
                ],
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
                    Подтверждено принятие искового заявления к производству<br>
                    Номер дела: {$input['courtCaseNumber']}<br>
                    Судья: {$input['judge']}<br>
                    Дата заседания: " . substr($input['dateMeeting'], 0, 2) . "-" . substr($input['dateMeeting'], 2, 2) . "-" . substr($input['dateMeeting'], 4, 4) . "<br>
                    Время заседания: " . substr($input['timeMeeting'], 0, 2) . ":" . substr($input['timeMeeting'], 2, 2);

            $currentFlatEvent
                ->setDateGenerate(new \DateTime())
                ->setData([
                    'confirm'           =>  true,
                    'courtCaseNumber'   =>  $input['courtCaseNumber'],
                    'judge'             =>  $input['judge'],
                    'dateMeeting'       =>  \DateTime::createFromFormat('dmY', $input['dateMeeting']),
                    'timeMeeting'       =>  $input['timeMeeting'],
                    'show'              =>  $showData
                ]);

            $flat = $currentFlatEvent->getFlat();
            $flat
                ->setEventDataParameter('courtCaseNumber', $input['courtCaseNumber'])
                ->setEventDataParameter('judge', $input['judge']);

            $this->em->persist($flat);

            $this->em->persist($currentFlatEvent);
            $this->em->flush();

            //добавляем лог - подтверждено принятие искового заявления
            $this->flatLogger->log($currentFlatEvent->getFlat(), "<b>{$this->event->getName()}</b><br>{$showData}");

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

        $showData = $this->twig->render('@App/Admin/Flat/EventLayer/verification_case_layer.html.twig', [
            'flat'  =>  $flat,
            'event' =>  $this->event
        ]);

        //удаляем событие "Подача искового заявления"
        $this->em->remove($flatEvent);

        //добавляем событие "Проверка принятия дела к производству"
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

        //добавляем лог - сгенерировалось событие "Проверка принятия дела к производству"
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
            return $eventRepository->findOneBy(['alias' => 'formation_statement_claim']);
        } elseif ($flatEvent->getParameter('confirm')) {
            return $eventRepository->findOneBy(['alias' =>  'legal_proceedings']);
        } else {
            return null;
        }
    }

    /**
     * @return array
     */
    protected function getMissData()
    {
        //при пропуске "Судебное делопроизводство" - ставим метку что оно выполнено, чтобы вывелось следующее событие
        return array_merge(parent::getMissData(), ['confirm' => true]);
    }
}