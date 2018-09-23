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

class SubmissionCommencementEnforcementProceedingsGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * SubmissionCommencementEnforcementProceedingsGenerator constructor.
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
        return 'submission_commencement_enforcement_proceedings';
    }

    /**
     * @param FlatEvent $flatEvent
     * @return int
     */
    public function getTimePerformAction(FlatEvent $flatEvent)
    {
        return 30;
    }

    /**
     * @param Request $request
     * @return bool|array
     */
    public function processUserAction(Request $request)
    {
        /** @var FlatEvent $currentFlatEvent */
        $currentFlatEvent = $this->getFlatEvent((int)$request->get('flat_id'));

        if (
            !$currentFlatEvent ||
            $currentFlatEvent->getParameter('confirm')
        ) {
            //действие уже выполнено
            return true;
        }

        $data = json_decode($request->getContent(), true);

        //входные данные
        $input = [
            'enforcementNumber' =>  $data['enforcementNumber'] ?? null,
            'bailiffName'       =>  $data['bailiffName'] ?? null,
            'bailiffContacts'   =>  $data['bailiffContacts'] ?? null,
            'deadline'          =>  $data['deadline'] ?? null
        ];

        $constraints = [
            'enforcementNumber'     =>  [
                new NotBlank(['message' => 'Укажите № исполнительного производства'])
            ],
            'bailiffName'           =>  [
                new NotBlank(['message' => 'Укажите ФИО пристава'])
            ],
            'bailiffContacts'       =>  [
                new NotBlank(['message' =>  'Укажите контакты пристава'])
            ],
            'deadline'              =>  [
                new NotBlank(['message' =>  'Укажите контрольный срок']),
                new Regex(['pattern'    => '/^([0-2]\d|3[01])(0\d|1[012])(19|20)(\d\d)$/', 'message' => 'Неверно указан контрольный срок'])
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

        $currentDate = new \DateTime();
        $showData = "
            Подача заявления в ФССП подтверждена {$currentDate->format('d.m.Y')}<br>
            № исполнительного производства: {$input['enforcementNumber']}<br>
            ФИО пристава: {$input['bailiffName']}<br>
            Контакты пристава: {$input['bailiffContacts']}<br>
            Контрольный срок: " . substr($input['deadline'], 0, 2) . "-" . substr($input['deadline'], 2, 2) . "-" . substr($input['deadline'], 4, 4);

        $currentFlatEvent
            ->setDateGenerate($currentDate)
            ->setData([
                'show'              =>  $showData,
                'enforcementNumber' =>  $input['enforcementNumber'],
                'bailiffName'       =>  $input['bailiffName'],
                'bailiffContacts'   =>  $input['bailiffContacts'],
                'deadline'          =>  \DateTime::createFromFormat('dmY', $input['deadline']),
                'confirm'           =>  true
            ]);

        //записываем в event_data дату подтверждения подачи заявления в ФССП
        $this->em->persist($currentFlatEvent->getFlat()->setEventDataParameter('submission_commencement_enforcement_proceedings', [
            'enforcement_number'    =>  $input['enforcementNumber'],
            'bailiff_name'          =>  $input['bailiffName'],
            'bailiff_contacts'      =>  $input['bailiffContacts'],
            'deadline'              =>  \DateTime::createFromFormat('dmY', $input['deadline']),
            'confirm'               =>  $currentDate
        ]));

        $this->em->persist($currentFlatEvent);
        $this->em->flush();

        //добавляем лог - подтверждено получение исполнительного листа
        $this->flatLogger->log($currentFlatEvent->getFlat(), "<b>{$this->event->getName()}</b><br>{$showData}");

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

        $showData = $this->twig->render('@App/Admin/Flat/EventLayer/submission_commencement_enforcement_proceedings_layer.html.twig', [
            'flat'  =>  $flat,
            'event' =>  $this->event
        ]);

        //удаляем событие "Заявление на возбуждение исполнительного производства"
        $this->em->remove($flatEvent);

        //добавляем событие "Подача заявления на возбуждение исполнительного производства"
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

        //добавляем лог - сгенерировалось событие "Подача заявления на возбуждение исполнительного производства"
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
            $this->em->getRepository('AppBundle:Event')->findOneBy(['alias' => 'control_enforcement_proceedings']) :
            null;
    }
}