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

class StatementCommencementEnforcementProceedingsGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * StatementCommencementEnforcementProceedingsGenerator constructor.
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
        return 'statement_commencement_enforcement_proceedings';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        if ($flatEvent->getParameter('deferred')) {
            //если обращение в ФССП отложено - вычисляем разницу между текущим временем и датой выполнения
            /** @var \DateTime $deadline */
            $deadline = $flatEvent->getParameter('deadline');

            return $deadline->diff(new \DateTime())->days;
        } elseif ($flatEvent->getParameter('perform')) {
            //если успешное обращение - через 3 дня выполняем следующее событие
            return 3;
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
            //если обращение в ФССП отложено - снова выполняем текущее событие
            return [$this];
        } elseif ($flatEvent->getParameter('perform')) {
            //если обращение в ФССП успешное - выполняем следующее событие
            $generatorAlias = 'submission_commencement_enforcement_proceedings';
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
            $currentFlatEvent->getParameter('perform')
            //если обращение в ФССП отложено - пользователь снова может указать вид работ с дату выполнения
        ) {
            //действие уже выполнено
            return true;
        }

        //если формирование выполнено - переходим к следующему событию
        if ($request->get('action') == 'perform') {
            $showData = "Выполнено формирование заявления на возбеждение исполнительного производства";
            $currentFlatEvent
                ->setDateGenerate(new \DateTime())
                ->setData([
                    'show'      =>  $showData,
                    'perform'   =>  true
                ]);

            $this->em->persist($currentFlatEvent);
            $this->em->flush();

            //добавляем лог - выполнено формирование заявления на возбеждение исполнительного производства
            $this->flatLogger->log($currentFlatEvent->getFlat(), "<b>{$this->event->getName()}</b><br>{$showData}");

            return true;
        } else {//если отложено обращение в ФССП - обращение переносится
            $data = json_decode($request->getContent(), true);

            //входные данные по переносу заседания
            $input = [
                'typeEvent' =>  $data['typeEvent'] ?? null,
                'deadline'  =>  $data['deadline'] ?? null
            ];

            $constraints = [
                'typeEvent'       =>  [
                    new NotBlank(['message' =>  'Укажите плановый вид мероприятия'])
                ],
                'deadline'       =>  [
                    new NotBlank(['message' =>  'Укажите дату выполнения']),
                    new Regex(['pattern'    => '/^([0-2]\d|3[01])(0\d|1[012])(19|20)(\d\d)$/', 'message' => 'Неверно указана дата выполнения'])
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
                Обращение в ФССП отложено<br>
                Плановый вид мероприятия: {$input['typeEvent']}<br>
                Дата выполнения: " . substr($input['deadline'], 0, 2) . "-" . substr($input['deadline'], 2, 2) . "-" . substr($input['deadline'], 4, 4);

            $currentFlatEvent
                ->setDateGenerate(new \DateTime())
                ->setData([
                   'show'       =>  $showData,
                    'deferred'  =>  true,
                    'typeEvent' =>  $input['typeEvent'],
                    'deadline'  =>  \DateTime::createFromFormat('dmY', $input['deadline'])
                ]);

            $this->em->persist($currentFlatEvent);
            $this->em->flush();

            //добавляем лог - отложено обращение в ФССП
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
            !$flatEvent->getParameter('perform') &&
            !$flatEvent->getParameter('deferred')
        ) {
            /** @var array $documentLinks */
            $documentLinks = $this->templateGenerator->generateTemplate($flat, $this->event);

            $showData = $this->twig->render('@App/Admin/Flat/EventLayer/statement_commencement_enforcement_proceedings_layer.html.twig', [
                'flat'              =>  $flat,
                'event'             =>  $this->event,
                'document_links'    =>  $documentLinks
            ]);

            //удаляем событие "Получение исполнительного листа" или "Получение судебного приказа"
            $this->em->remove($flatEvent);

            //добавляем событие "Заявление на возбуждение сполнительного производства"
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
            //если обращение было отложено - снова генерируем это же событие только без генерации документа
            $showData = $this->twig->render('@App/Admin/Flat/EventLayer/statement_commencement_enforcement_proceedings_layer.html.twig', [
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

        //добавляем лог - сгенерировалось событие "Заявление на возбуждение сполнительного производства"
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}");

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
        } elseif ($flatEvent->getParameter('perform')) {
            return $this->em->getRepository('AppBundle:Event')->findOneBy(['alias' => 'submission_commencement_enforcement_proceedings']);
        } else {
            return null;
        }
    }

    /**
     * @return array
     */
    protected function getMissData()
    {
        //при пропуске "Подача заявления на возбуждение исполнительного производства" - ставим метку подстверждения, чтобы вывелось следующее событие
        return array_merge(parent::getMissData(), ['confirm' => true]);
    }
}