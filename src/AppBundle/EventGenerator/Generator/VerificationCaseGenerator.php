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

class VerificationCaseGenerator extends BaseGenerator implements GeneratorInterface
{
    /** @var TwigEngine  */
    private $twig;

    /** @var ValidatorInterface  */
    private $validator;

    /** @var DataCollectorTranslator  */
    private $translator;

    /**
     * VerificationCaseGenerator constructor.
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
        parent::__construct($em, $flatLogger, $router, $templateGenerator);
        $this->twig = $twig;
        $this->validator = $validator;
        $this->translator = $translator;
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
        if ($flatEvent->getParameter('failure', false)) {
            //если пользователь выбрал отказ - генерируем следующее событие при следующем выполнении таски
            return 0;
        } elseif ($flatEvent->getParameter('confirm', false)) {
            //если пользователь выбрал принятие - генерируем в дату заседания
            return 12;
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
        if ($flatEvent->getParameter('failure', false)) {
            $generatorAlias = 'formation_statement_claim';
        } elseif ($flatEvent->getParameter('confirm', false)) {
            $generatorAlias = '';
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
            $flatEvent->getParameter('confirm', false) ||
            $flatEvent->getParameter('failure', false)
        ) {
            //действие выполнено
            return true;
        }

        //если отказ
        if ($request->get('action') == 'failure') {
            $showData = "Отказано";
            $currentFlatEvent
                ->setData([
                    'show'      =>  $showData,
                    'failure'   =>  true
                ]);

            $this->em->persist($currentFlatEvent);
            $this->em->flush();

            //добавляем лог - отказ принятия искового заявления
            $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

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
                    Подтверждено<br>
                    Номер дела: {$input['courtCaseNumber']}<br>
                    Судья: {$input['judge']}<br>
                    Дата заседания: {$input['dateMeeting']},
                    Время заседания: {$input['timeMeeting']}
                    ";

            $currentFlatEvent
                ->setData([
                    'confirm'           =>  true,
                    'courtCaseNumber'   =>  $input['courtCaseNumber'],
                    'judge'             =>  $input['judge'],
                    'dateMeeting'       =>  \DateTime::createFromFormat('dmY', $input['dateMeeting']),
                    'timeMeeting'       =>  $input['timeMeeting'],
                    'show'              =>  $showData
                ]);

            $this->em->persist($currentFlatEvent);
//            $this->em->flush();

            //добавляем лог - подтверждено принятие искового заявления
            $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

            return [
                'success'   =>  true,
                'errors'    =>  false
            ];
        }
    }

    /**
     * @param Flat $flat
     * @param FlatEvent $flatEvent
     * @return bool
     */
    public function eventGenerate(Flat $flat, FlatEvent $flatEvent)
    {
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
}