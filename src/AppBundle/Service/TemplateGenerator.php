<?php

namespace AppBundle\Service;

use AppBundle\Entity\Debtor;
use AppBundle\Entity\Event;
use AppBundle\Entity\Flat;
use AppBundle\Entity\Subscriber;
use AppBundle\Exception\NoDebtorsException;
use AppBundle\Exception\NoSubscribersException;
use Doctrine\ORM\EntityManager;
use Knp\Bundle\SnappyBundle\Snappy\LoggableGenerator;

class TemplateGenerator
{
    //текст сообщения, если не указано
    const UNDEFINED = 'Не указано';
    //данные для полей подставновки извлекаем из
    const FLAT          = 'flat';//помещения
    const SUBSCRIBER    = 'subscriber';//абонента
    const DEBTOR        = 'debtor';//должника
    const MIXED         = 'mixed';//абонетна или должника

    /** @var array алиасы полей подстановки в шаблон */
    private static $templateFields = [
        'street'                    =>  [
            'title' =>  'Улица',
            'type'  =>  self::FLAT
        ],
        'house_number'              =>  [
            'title' =>  'Номер дома',
            'type'  =>  self::FLAT
        ],
        'flat_number'               =>  [
            'title' =>  'Квартира (комната)',
            'type'  =>  self::FLAT
        ],
        'subscriber'                =>  [
            'title' =>  'Абонент',
            'type'  =>  self::SUBSCRIBER
        ],
        'personal_account'          =>  [
            'title' =>  'Лицевой счет',
            'type'  =>  self::MIXED
        ],
        'personal_account_status'   =>  [
            'title' =>  'Статус лицевого счета',
            'type'  =>  self::MIXED
        ],
        'debtor'                    =>  [
            'title' =>  'Должник',
            'type'  =>  self::DEBTOR
        ],
        'debtor_type'               =>  [
            'title' =>  'Субъект',
            'type'  =>  self::DEBTOR
        ],
        'ownership_status'          =>  [
            'title' =>  'Статус',
            'type'  =>  self::DEBTOR
        ],
        'debtor_info'               =>  [
            'title' =>  'Информация о должнике (ОГРН/ОГРНИП/ИНН/Место рожд/жит-ва/доля и пр)',
            'type'  =>  self::DEBTOR
        ],
        'mkd_management_start_date' =>  [
            'title' =>  'Дата начала управления МКД',
            'type'  =>  self::FLAT
        ],
        'mkd_management_end_date'   =>  [
            'title' =>  'Дата окончания управления МКД',
            'type'  =>  self::FLAT
        ],
        'mkd_legal_document_name'   =>  [
            'title' =>  'Документ на право управления МКД',
            'type'  =>  self::FLAT
        ],
        'start_debt_period'         =>  [
            'title' =>  'Начало периода взыскания',
            'type'  =>  self::FLAT
        ],
        'end_debt_period'           =>  [
            'title' =>  'Конец периода взыскания',
            'type'  =>  self::FLAT
        ],
        'period_accrued_debt'       =>  [
            'title' =>  'Начислено за период (долг)',
            'type'  =>  self::DEBTOR
        ],
        'period_accrued_fine'       =>  [
            'title' =>  'Начислено за период (пени)',
            'type'  =>  self::DEBTOR
        ],
        'period_pay_debt'           =>  [
            'title' =>  'Оплачено за период (долг)',
            'type'  =>  self::DEBTOR
        ],
        'period_pay_fine'           =>  [
            'title' =>  'Оплачено за период (пени)',
            'type'  =>  self::DEBTOR
        ],
        'sum_debt'                  =>  [
            'title' =>  'Размер долга',
            'type'  =>  self::DEBTOR
        ],
        'sum_fine'                  =>  [
            'title' =>  'Размер пени',
            'type'  =>  self::DEBTOR
        ],
        'judicial_sector_name'      =>  [
            'title' =>  'Суд',
            'type'  =>  self::FLAT
        ],
        'judicial_sector_address'   =>  [
            'title' =>  'Адрес суда',
            'type'  =>  self::FLAT
        ],
        //пошлина (ИскП) ????
        //пошлина (ПрикП) ????
        //полшлина (ИскП) ????
        //дата получения приказа ????
        //номер судебного приказа ????
        //дата судебного приказа ????
        //взыскано (долг) ????
        //взыскано (пени) ????
        //взыскано (пошлина) ????
        'fssp_department_name'      =>  [
            'title' =>  'Отделение ФССП',
            'type'  =>  self::FLAT
        ],
        'fssp_department_address'   =>  [
            'title' =>  'Адрес отделения ФССП',
            'type'  =>  self::FLAT
        ]
        //дата подачи приказа в ФССП ????
        //исполнительное производство ????
        //итого задолженность ????
        //итого пошлина ????
    ];

    /** @var EntityManager  */
    private $em;

    /** @var LoggableGenerator  */
    private $pdfGenerator;

    /** @var string  */
    private $rootDir;

    /**
     * TemplateGenerator constructor.
     * @param EntityManager $em
     * @param LoggableGenerator $pdfGenerator
     * @param $rootDir
     */
    public function __construct(
        EntityManager $em,
        LoggableGenerator $pdfGenerator,
        $rootDir
    ) {
        $this->em = $em;
        $this->pdfGenerator = $pdfGenerator;
        $this->rootDir = $rootDir . '/../web';
    }

    /**
     * получение доступных полей для шаблона
     * @param Event $event
     * @return array
     */
    public function getTemplateFields(Event $event)
    {
        $templateFields = [];

        foreach (self::$templateFields as $fieldAlias => $fieldParams) {
            if (
                ($event->getType() == Event::PRETENSE_ALIAS && $fieldParams['type'] != self::DEBTOR) ||//если у события тип "Претензия" - выводим все поля кроме типа "debtor"
                ($event->getType() != Event::PRETENSE_ALIAS && $fieldParams['type'] != self::SUBSCRIBER)//если у события тип не "Претензия" - выводим все поля кроме типа "subscriber"
            ) {
                $templateFields[$fieldParams['title'] . ' {{' . $fieldAlias . '}}'] = $fieldAlias;
            }
        }

        return $templateFields;
    }

    /**
     * генерация pdf документа
     * @param Flat $flat
     * @param Event $event
     * @return array
     * @throws NoDebtorsException
     * @throws NoSubscribersException
     */
    public function generateTemplate(Flat $flat, Event $event)
    {
        //если тип события "Претензия" - достаем список абонентов, иначе должников
        $subjects = $event->getType() == Event::PRETENSE_ALIAS ?
            $flat->getSubscribers() :
            $flat->getDebtors();

        if ($event->getType() == Event::PRETENSE_ALIAS && !count($subjects)) {
            throw new NoSubscribersException("Список абонентов пуст, для генерации шаблона '{$event->getName()}' нужно добавить хотя бы одного абонента");
        }

        if ($event->getType() != Event::PRETENSE_ALIAS && !count($subjects)) {
            throw new NoDebtorsException("Список должников пуст, для генерации шаблона '{$event->getName()}' необходимо добавить хотя бы одного должника");
        }

        $pdfLinks = [];

        /** @var Debtor|Subscriber $subject */
        foreach ($subjects as $subject) {
            /** @var string $template */
            $template = $event->getTemplate();

            /** @var string $field */
            foreach ($event->getTemplateFields() as $field) {
                $fieldValueMethod = $this->getFieldValueMethod($field);//вызываемый метод для получения значения подстановки
                $fieldValueMethodParam = self::$templateFields[$field]['type'] == self::FLAT ? $flat : $subject;//передаваемый параметр

                $template = $this->templateReplace(
                    '{{' . $field . '}}',
                    $this->$fieldValueMethod($fieldValueMethodParam),
                    $template
                );
            }

            $pdfDir = '/pdf/' . $flat->getId() . '/' . $event->getAlias() . '_' . md5(uniqid()) . '.pdf';

            //генерация pdf
            /*$this->pdfGenerator->generateFromHtml(
                $this->wrapUpTemplate($template),
                $this->rootDir . $pdfDir
            );*/

            $pdfLinks[] = $pdfDir;
        }

        return $pdfLinks;
    }

    /**
     * получение метода для получения значения поля подстановки
     * @param $field
     * @return string
     */
    private function getFieldValueMethod($field)
    {
        return 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field))) . 'FieldValue';
    }

    /**
     * поиск и замена подстроки в строке
     * @param $search
     * @param $replace
     * @param $text
     * @return string
     */
    private function templateReplace($search, $replace, $text)
    {
        $position = strpos($text, $search);

        return $position === false ? $text : substr_replace($text, $replace, $position, mb_strlen($search));
    }

    /**
     * оборачивание шаблона в обертку
     * @param $template
     * @return string
     */
    private function wrapUpTemplate($template)
    {
        return '<!doctype html>
                <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                        <meta http-equiv="X-UA-Compatible" content="ie=edge">
                        <meta charset="UTF-8" />
                    </head>
                    <body>' .
                        $template .
                    '</body>
                </html>';
    }

    /**
     * название улицы
     * @param Flat $flat
     * @return string
     */
    private function getStreetFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getStreet()->getTitle();
    }

    /**
     * номер дома
     * @param Flat $flat
     * @return string
     */
    private function getHouseNumberFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getNumber();
    }

    /**
     * номер помещения
     * @param Flat $flat
     * @return string
     */
    private function getFlatNumberFieldValue(Flat $flat)
    {
        return $flat->getNumber();
    }

    /**
     * ФИО абонента
     * @param Subscriber $subscriber
     * @return string
     */
    private function getSubscriberFieldValue(Subscriber $subscriber)
    {
        return $subscriber->getName();
    }

    /**
     * лицевой счет
     * @param $object
     * @return string
     */
    private function getPersonalAccountFieldValue($object)
    {
        return $object->getPersonalAccount()->getAccount();
    }

    /**
     * статус лицевого счета
     * @param $object
     * @return string
     */
    private function getPersonalAccountStatusFieldValue($object)
    {
        $currentSubscriber = $object;

        if ($object instanceof Debtor) {
            $debtorPersonalAccount = $object->getPersonalAccount()->getAccount();

            /** @var Subscriber $subscriber */
            foreach ($object->getFlat()->getSubscribers() as $subscriber) {
                if ($subscriber->getPersonalAccount()->getAccount() == $debtorPersonalAccount) {
                    $currentSubscriber = $subscriber;
                    break;
                }
            }
        }

        return $currentSubscriber->getDateCloseAccount() && $currentSubscriber->getDateCloseAccount() < new \DateTime() ?
            'Закрыт' :
            'Открыт';
    }

    /**
     * ФИО должника
     * @param Debtor $debtor
     * @return string
     */
    private function getDebtorFieldValue(Debtor $debtor)
    {
        return $debtor->getName();
    }

    /**
     * Субъект
     * @param Debtor $debtor
     * @return string
     */
    private function getDebtorTypeFieldValue(Debtor $debtor)
    {
        return $debtor->getType()->getTitle();
    }

    /**
     * статус собственности
     * @param Debtor $debtor
     * @return string
     */
    private function getOwnershipStatusFieldValue(Debtor $debtor)
    {
        $ownerShipStatus = $debtor->getOwnershipStatus();

        //если нужно будет вывести родительский статус - раскомментить
        /*while ($parent = $ownerShipStatus->getParent()) {
            $ownerShipStatus = $parent;
        }*/

        return $ownerShipStatus->getTitle();
    }

    /**
     * информация о должнике
     * @param Debtor $debtor
     * @return string
     */
    private function getDebtorInfoFieldValue(Debtor $debtor)
    {
        $debtorInfo = '';
        $ownershipStatus = $debtor->getOwnershipStatus();

        if ($ownershipStatus->getAlias() == 'owner_shared') {
            $debtorInfo .= 'Доля: ' . $debtor->getShareSize() . '<br>';
        }

        switch ($debtor->getType()->getAlias()) {
            case 'individual':
                $debtorInfo .=
                    'Дата рождения: ' . ($debtor->getDateOfBirth() ? $debtor->getDateOfBirth()->format('d.m.Y') : self::UNDEFINED) . '<br>' .
                    'Место рождения: ' . ($debtor->getPlaceOfBirth() ?: self::UNDEFINED) . '<br>' .
                    'Место жительства: ' . ($debtor->getLocation() ?: self::UNDEFINED) . '<br>';
                break;
            case 'businessman':
                $debtorInfo .=
                    'ОГРНИП: ' . ($debtor->getOgrnip() ?: self::UNDEFINED) . '<br>' .
                    'ИНН: ' . ($debtor->getInn() ?: self::UNDEFINED) . '<br>' .
                    'Место жительства: ' . ($debtor->getLocation() ?: self::UNDEFINED) . '<br>';
                break;
            case 'legal':
                $debtorInfo .=
                    'ОГРН: ' . ($debtor->getOgrn() ?: self::UNDEFINED) . '<br>' .
                    'ИНН: ' . ($debtor->getInn() ?: self::UNDEFINED) . '<br>' .
                    'Место нахождения: ' . ($debtor->getLocation() ?: self::UNDEFINED) . '<br>';
                break;
        }

        return $debtorInfo;
    }

    /**
     * дата начала управления МКД
     * @param Flat $flat
     * @return string
     */
    private function getMkdManagementStartDateFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getMkd()->getManagementStartDate() ?
            $flat->getHouse()->getMkd()->getManagementStartDate()->format('d.m.Y') :
            self::UNDEFINED;
    }

    /**
     * дата окончания управления МКД
     * @param Flat $flat
     * @return string
     */
    private function getMkdManagementEndDateFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getMkd()->getManagementEndDate() ?
            $flat->getHouse()->getMkd()->getManagementEndDate()->format('d.m.Y') :
            self::UNDEFINED;
    }

    /**
     * название документа на право управления МКД
     * @param Flat $flat
     * @return string
     */
    private function getMkdLegalDocumentNameFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getMkd() ?
            $flat->getHouse()->getMkd()->getLegalDocumentName() :
            self::UNDEFINED;
    }

    /**
     * начало периода взыскания
     * @param Flat $flat
     * @return string
     */
    private function getStartDebtPeriodFieldValue(Flat $flat)
    {
        return $flat->getStartDebtPeriod() ?
            $flat->getStartDebtPeriod()->format('d.m.Y') :
            self::UNDEFINED;
    }

    /**
     * конец периода взыскания
     * @param Flat $flat
     * @return string
     */
    private function getEndDebtPeriodFieldValue(Flat $flat)
    {
        return $flat->getEndDebtPeriod() ?
            $flat->getEndDebtPeriod()->format('d.m.Y') :
            self::UNDEFINED;
    }

    /**
     * начислено за период (долг)
     * @param Debtor $debtor
     * @return string
     */
    private function getPeriodAccruedDebtFieldValue(Debtor $debtor)
    {
        //есть вопрос по расчету
        $flat = $debtor->getFlat();

        if (!$flat->getPeriodAccruedDebt()) {
            return self::UNDEFINED;
        }

        $periodAccruedDebt = $flat->getPeriodAccruedDebt();

        //если долевой собственник - делим долги на всех
        if ($debtor->getOwnershipStatus()->getAlias() == 'owner_shared') {
            //количество собственников - вычисляем из размера доли
            $size = (int)explode('/', $debtor->getShareSize())[1];

            $periodAccruedDebt = $periodAccruedDebt/$size;
        }

        return number_format($periodAccruedDebt, 2, '.', ' ');
    }

    /**
     * начислено за период (пени)
     * @param Debtor $debtor
     * @return string
     */
    private function getPeriodAccruedFineFieldValue(Debtor $debtor)
    {
        //есть вопрос по расчету
        $flat = $debtor->getFlat();

        if (!$flat->getPeriodAccruedFine()) {
            return self::UNDEFINED;
        }

        $periodAccruedFine = $flat->getPeriodAccruedFine();

        //если долевой собственник - делим долги на всех
        if ($debtor->getOwnershipStatus()->getAlias() == 'owner_shared') {
            //количество собственников - вычисляем из размера доли
            $size = (int)explode('/', $debtor->getShareSize())[1];

            $periodAccruedFine = $periodAccruedFine/$size;
        }

        return number_format($periodAccruedFine, 2, '.', ' ');
    }

    /**
     * за период оплачено долга
     * @param Debtor $debtor
     * @return string
     */
    private function getPeriodPayDebtFieldValue(Debtor $debtor)
    {
        //есть вопрос по расчету
        $flat = $debtor->getFlat();

        if (!$flat->getPeriodPayDebt()) {
            return self::UNDEFINED;
        }

        return number_format($flat->getPeriodPayDebt(), 2, '.', ' ');
    }

    /**
     * за период оплачено пени
     * @param Debtor $debtor
     * @return string
     */
    private function getPeriodPayFineFieldValue(Debtor $debtor)
    {
        //есть вопрос по расчету
        $flat = $debtor->getFlat();

        if (!$flat->getPeriodPayFine()) {
            return self::UNDEFINED;
        }

        return number_format($flat->getPeriodPayFine(), 2, '.', ' ');
    }

    /**
     * размер долга
     * @param Debtor $debtor
     * @return string
     */
    private function getSumDebtFieldValue(Debtor $debtor)
    {
        //есть вопрос по расчету
        $flat = $debtor->getFlat();

        if (!$flat->getSumDebt()) {
            return self::UNDEFINED;
        }

        $sumDebt = $flat->getSumDebt();

        //если долевой собственник - делим долги на всех
        if ($debtor->getOwnershipStatus()->getAlias() == 'owner_shared') {
            //количество собственников - вычисляем из размера доли
            $size = (int)explode('/', $debtor->getShareSize())[1];

            $sumDebt = $sumDebt/$size;
        }

        return number_format($sumDebt, 2, '.', ' ');
    }

    /**
     * размер пени
     * @param Debtor $debtor
     * @return string
     */
    private function getSumFineFieldValue(Debtor $debtor)
    {
        $flat = $debtor->getFlat();

        if (!$flat->getSumFine()) {
            return self::UNDEFINED;
        }

        $sumFine = $flat->getSumFine();

        //если долевой собственник - делим долги на всех
        if ($debtor->getOwnershipStatus()->getAlias() == 'owner_shared') {
            //количество собственников - вычисляем из размера доли
            $size = (int)explode('/', $debtor->getShareSize())[1];

            $sumFine = $sumFine/$size;
        }

        return number_format($sumFine, 2, '.', ' ');
    }

    /**
     * суд
     * @param Flat $flat
     * @return string
     */
    private function getJudicialSectorNameFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getMkd()->getJudicialSector()->getName();
    }

    /**
     * адрес суда
     * @param Flat $flat
     * @return string
     */
    private function getJudicialSectorAddressFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getMkd()->getJudicialSector()->getAddress();
    }

    /**
     * отделение ФССП
     * @param Flat $flat
     * @return string
     */
    private function getFsspDepartmentNameFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getMkd()->getFsspDepartment()->getName();
    }

    /**
     * адрес ФССП
     * @param Flat $flat
     * @return string
     */
    private function getFsspDepartmentAddressFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getMkd()->getFsspDepartment()->getAddress();
    }
}