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
        if ($flatEvent->getParameter('appealed')) {
            //если обжаловано получение исполнительного листа - вычисляем разницу между текущим временем и датой заседания
            /** @var \DateTime $dateMeeting */
            $dateMeeting = $flatEvent->getParameter('dateMeeting');

            return $dateMeeting->diff(new \DateTime())->days;
        } elseif ($flatEvent->getParameter('not_appealed')) {
            //если не обжаловано получение исполнительного листа - через 7 дней переходим к следующему событию
            return 7;
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
        if ($flatEvent->getParameter('appealed')) {
            //если обжаловано заявление на получение исполнительного листа - снова генерим текущее событие
            return [$this];
        } elseif ($flatEvent->getParameter('not_appealed')) {
            //если не обжаловано заявление на получение исполнительного листа - выполняем следующее событие
            $generatorAlias = 'obtaining_writ_execution';
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
            $currentFlatEvent->getParameter('not_appealed')
            //если заявление на получение исполнительного листа обжаловано - пользователь может снова указать дату след заседания
        ) {
            //действие уже выполнено
            return true;
        }

        //если не обжаловано - переходим к следующему событию
        if ($request->get('action') == 'not_appealed') {
            $showData = "Решение вступилов в силу (не обжаловано)";
            $currentFlatEvent
                ->setData([
                    'show'          =>  $showData,
                    'not_appealed'  =>  true
                ]);

            $this->em->persist($currentFlatEvent);
            $this->em->flush();

            //добавляем лог - не обжаловано заявление на получение исполнительного листа
            $this->flatLogger->log($currentFlatEvent->getFlat(), "<b>{$this->event->getName()}</b><br>{$showData}");

            return true;
        } else {//если обжаловано - заседаные переносится
            $data = json_decode($request->getContent(), true);

            //входные данные по переносу заседания
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
                Решение не вступило в силу (обжаловано)<br>
                Дата заседания: " . substr($input['dateMeeting'], 0, 2) . "-" . substr($input['dateMeeting'], 2, 2) . "-" . substr($input['dateMeeting'], 4, 4) . "<br>
                Время заседания: " . substr($input['timeMeeting'], 0, 2) . ":" . substr($input['timeMeeting'], 2, 2);

            $currentFlatEvent
                ->setData([
                    'appealed'          =>  true,
                    'dateMeeting'       =>  \DateTime::createFromFormat('dmY', $input['dateMeeting']),
                    'timeMeeting'       =>  $input['timeMeeting'],
                    'show'              =>  $showData
                ]);

            $this->em->persist($currentFlatEvent);
            $this->em->flush();

            //добавляем лог - обжаловано заявление на получение исполнительного листа
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
        $this->validateEvent();

        /** @var Flat $flat */
        $flat = $flatEvent->getFlat();

        //если не было никаких действий по этому событию - генерируем документ
        if (
            !$flatEvent->getParameter('appealed') &&
            !$flatEvent->getParameter('not_appealed')
        ) {
            /** @var array $documentLinks */
            $documentLinks = $this->templateGenerator->generateTemplate($flat, $this->event);

            $showData = $this->twig->render('@App/Admin/Flat/EventLayer/statement_receipt_writ_execution_layer.html.twig', [
                'flat'              =>  $flat,
                'event'             =>  $this->event,
                'document_links'    =>  $documentLinks
            ]);

            //удаляем событие "Судебное делопроизводство"
            $this->em->remove($flatEvent);

            //добавляем событие "Заявление на получение исполнительного листа"
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
        } else {
            //если обжаловано - снова генерируем это же событие только без генерации документов
            $showData = $this->twig->render('@App/Admin/Flat/EventLayer/statement_receipt_writ_execution_layer.html.twig', [
                'flat'              =>  $flat,
                'event'             =>  $this->event,
                'document_links'    =>  []
            ]);

            $flatEvent
                ->setDateGenerate(new \DateTime())
                ->setParameter('show', $showData);

            $this->em->persist($flatEvent);
            $this->em->flush();
        }

        //добавляем лог - сгенерировалось событие "Заявление на получение исполнительного листа"
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

        return true;
    }

    /**
     * @param FlatEvent $flatEvent
     * @return \AppBundle\Entity\Event|null|object
     */
    public function getNextEvent(FlatEvent $flatEvent)
    {
        if ($flatEvent->getParameter('appealed')) {
            return $this->event;
        } elseif ($flatEvent->getParameter('not_appealed')) {
            return $this->em->getRepository('AppBundle:Event')->findOneBy(['alias' => 'obtaining_writ_execution']);
        } else {
            return null;
        }
    }

    /**
     * @return array
     */
    protected function getMissData()
    {
        //при пропуске "Получение исполнительного листа" - ставим метку подстверждения, чтобы вывелось следующее событие
        return array_merge(parent::getMissData(), ['confirm' => true]);
    }
}