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
     * @return array|bool
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

        $data = json_decode($request->getContent(), true);

        $input = [
            'courtOrderNumber'   =>  $data['courtOrderNumber'] ?? null,
            'courtOrderDate'     =>  $data['courtOrderDate'] ?? null
        ];

        $constraints = [
            'courtOrderNumber'   =>  [
                new NotBlank(['message' => 'Укажите номер приказа'])
            ],
            'courtOrderDate'     =>  [
                new NotBlank(['message' =>  'Укажите дату получения приказа']),
                new Regex(['pattern'    => '/^([0-2]\d|3[01])(0\d|1[012])(19|20)(\d\d)$/', 'message' => 'Неверно указана дата получения приказа'])
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

        //записываем в event_data - дату подачи заявления судебного приказа в суд
        $this->em->persist($currentFlatEvent->getFlat()->setEventDataParameter('applying_court_order', [
            'courtOrderNumber'  =>  $input['courtOrderNumber'],
            'courtOrderDate'    =>  \DateTime::createFromFormat('dmY', $input['courtOrderDate']),
            'confirm'           =>  new \DateTime()
        ]));

        $showData = "
            Подача заявления судебного прикза подтверждена<br>
            Номер приказа: " . $input['courtOrderNumber'] . "<br>
            Дата получения приказа: " . substr($input['courtOrderDate'], 0, 2) . "." . substr($input['courtOrderDate'], 2, 2) . "." . substr($input['courtOrderDate'], 4, 4);

        $currentFlatEvent
            ->setDateGenerate(new \DateTime())
            ->setData([
                'show'                  =>  $showData,
                'courtOrderNumber'      =>  $input['courtOrderNumber'],
                'courtOrderDate'        =>  \DateTime::createFromFormat('dmY', $input['courtOrderDate']),
                'confirm'               =>  true//подтверждено - можно через 40 дней выполнять следующее событие
            ]);

        $this->em->persist($currentFlatEvent);
        $this->em->flush();

        //добавляем лог - что все подтверждения подача заявления на СП в суд
        $this->flatLogger->log($currentFlatEvent->getFlat(), "<b>{$this->event->getName()}</b><br>{$showData}", $this->event);

        return [
            'success'   =>  true,
            'errors'    =>  false
        ];
    }

    /**
     * @param FlatEvent $flatEvent
     * @return bool
     */
    public function generateEvent(FlatEvent $flatEvent)
    {
        /** @var Flat $flat */
        $flat = $flatEvent->getFlat();

        $showData = $this->twig->render('@App/Admin/Flat/EventLayer/applying_court_order_layer.html.twig', [
            'flat'  =>  $flat,
            'event' =>  $this->event
        ]);

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
        $this->flatLogger->log($flat, "<b>{$this->event->getName()}</b><br>{$showData}", $this->event);

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