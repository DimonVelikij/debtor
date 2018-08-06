<?php

namespace AppBundle\Service;

use AppBundle\Entity\Debtor;
use AppBundle\Entity\Event;
use AppBundle\Entity\Flat;
use AppBundle\Entity\Subscriber;
use AppBundle\EventGenerator\EventType;
use AppBundle\Exception\NoDebtorsException;
use AppBundle\Exception\NoSubscribersException;
use Doctrine\ORM\EntityManager;
use Knp\Bundle\SnappyBundle\Snappy\LoggableGenerator;

class TemplateGenerator
{
    /** @var array алиасы полей подстановки в шаблон */
    private static $templateFieldAliases = [
        'city',
        'street',
        'house_number',
        'flat_number',
        'subscriber',
        'personal_account',
        //статус лицевого счета
        'debtor',
        //субъект
        //статус
        //доля
        //дата рожд ОГРН ОГРНИП
        //место рождения ИНН КПП
        //место жительства
        'management_start_date',
        'management_end_date',
        'legal_document_name',
        'start_debt_period',
        'end_debt_period',
        'period_accrued_debt',
        'period_accrued_fine',
        'period_pay_debt',
        'period_pay_fine',
        'sum_debt',
        'sum_fine',
        'judicial_sector_name',
        'judicial_sector_address',
        //пошлина (ИскП) ????
        //пошлина (ПрикП) ????
        //полшлина (ИскП) ????
        //дата получения приказа
        //номер судебного приказа
        //дата судебного приказа
        //взыскано (долг) ????
        //взыскано (пени) ????
        //взыскано (пошлина) ????
        'fssp_department_name',
        'fssp_department_address',
        //дата подачи приказа в ФССП ????
        //исполнительное производство ????
        //итого задолженность ????
        //итого пошлина ????
    ];

    /** @var array названия полей подстановки в шаблон */
    private static $templateFieldTitles = [
        'Город',
        'Улица',
        'Номер дома',
        'Номер помещения',
        'Лицевой счет',
        'Абонент',
        'Должник',
        'Дата начала управления МКД',
        'Дата окончанию управления МКД',
        'Документ на право управления МКД',
        'Начало периода взыскания',
        'Конец периода взыскания',
        'Начислено за период (долг)',
        'Начислено за период (пени)',
        'Оплачено за период (долг)',
        'Оплачено за период (пени)',
        'Размер долга',
        'Размер пени',
        'Суд',
        'Адрес суда',
        'Отделение ФССП',
        'Адрес отделения ФССП'
    ];

    /** @var array какую сущность использовать для получения значения поля подстановки */
    private static $templateFieldValueEntity = [
        'city'              => 'flat',
        'street'            => 'flat',
        'house_number'      => 'flat',
        'flat_number'       => 'flat',
        'personal_account'  =>  false,
        'subscriber'        =>  false,
        'debtor'            =>  false,
        'management_start_date' =>  'flat',
        'management_end_date'   =>  'flat',
        'legal_document_name'   =>  'flat',
        'start_debt_period'     =>  'flat',
        'end_debt_period'       =>  'flat',
        'period_accrued_debt'   =>  'flat',
        'period_accrued_fine'   =>  'flat',
        'period_pay_debt'       =>  'flat',
        'period_pay_fine'       =>  'flat',
        'sum_debt'              =>  'flat',
        'sum_fine'              =>  'flat',
        'judicial_sector_name'  =>  'flat',
        'judicial_sector_address'   =>  'flat',
        'fssp_department_name'      =>  'flat',
        'fssp_department_address'   =>  'flat'
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
     * @return array
     */
    public function getTemplateFields()
    {
        $templateFields = [];

        for ($i = 0; $i < count(TemplateGenerator::$templateFieldAliases); $i++) {
            $templateFields[TemplateGenerator::$templateFieldTitles[$i] . ' {{' . TemplateGenerator::$templateFieldAliases[$i] . '}}'] = TemplateGenerator::$templateFieldAliases[$i];
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
        $subjects = $event->getType() == EventType::PRETENSE_ALIAS ?
            $flat->getSubscribers() :
            $flat->getDebtors();

        if ($event->getType() == EventType::PRETENSE_ALIAS && !count($subjects)) {
            throw new NoSubscribersException("Список абонентов пуст, для генерации шаблона '{$event->getName()}' нужно добавить хотя бы одного абонента");
        }

        if ($event->getType() != EventType::PRETENSE_ALIAS && !count($subjects)) {
            throw new NoDebtorsException("Список должников пуст, для генерации шаблона '{$event->getName()}' необходимо добавить хотя бы одного должника");
        }

        $pdfLinks = [];

        /** @var Debtor|Subscriber $subject */
        foreach ($subjects as $subject) {
            /** @var string $template */
            $template = $event->getTemplate();

            /** @var string $field */
            foreach ($event->getTemplateFields() as $field) {
                $fieldValueMethod = $this->getFieldValueMethod($field);

                $template = $this->templateReplace(
                    '{{' . $field . '}}',
                    $this->$fieldValueMethod(
                        TemplateGenerator::$templateFieldValueEntity[$field] == 'flat' ?
                            $flat :
                            $subject
                    ),
                    $template
                );
            }

            $pdfDir = '/pdf/' . $flat->getId() . '/' . $event->getAlias() . '_' . md5(uniqid()) . '.pdf';

            //генерация pdf
            $this->pdfGenerator->generateFromHtml(
                $this->wrapUpTemplate($template),
                $this->rootDir . $pdfDir
            );

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
     * название города
     * @param Flat $flat
     * @return string
     */
    private function getCityFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getStreet()->getCity()->getTitle();
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
     * лицевой счет
     * @param $object
     * @return string
     */
    private function getPersonalAccountFieldValue($object)
    {
        return $object->getPersonalAccount()->getAccount();
    }

    /**
     * ФИО абонента
     * @param Subscriber $object
     * @return string
     */
    private function getSubscriberFieldValue(Subscriber $object)
    {
        return $object->getName();
    }

    /**
     * ФИО должника
     * @param Debtor $object
     * @return string
     */
    private function getDebtorFieldValue(Debtor $object)
    {
        return $object->getName();
    }

    /**
     * дата начала управления МКД
     * @param Flat $flat
     * @return string
     */
    private function getManagementStartDateFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getMkd()->getManagementStartDate()->format('d.m.Y');
    }

    /**
     * дата окончания управления МКД
     * @param Flat $flat
     * @return string
     */
    private function getManagementEndDateFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getMkd()->getManagementEndDate() ?
            $flat->getHouse()->getMkd()->getManagementEndDate()->format('d.m.Y') :
            '';
    }

    /**
     * название документа на право управления МКД
     * @param Flat $flat
     * @return string
     */
    private function getLegalDocumentNameFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getMkd()->getLegalDocumentName();
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
            '';
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
            '';
    }

    /**
     * начислено за период (долг)
     * @param Flat $flat
     * @return float
     */
    private function getPeriodAccruedDebtFieldValue(Flat $flat)
    {
        return $flat->getPeriodAccruedDebt();
    }

    /**
     * начислено за период (пени)
     * @param Flat $flat
     * @return float
     */
    private function getPeriodAccruedFineFieldValue(Flat $flat)
    {
        return $flat->getPeriodAccruedFine();
    }

    /**
     * за период оплачено долга
     * @param Flat $flat
     * @return float
     */
    private function getPeriodPayDebtFieldValue(Flat $flat)
    {
        return $flat->getPeriodPayDebt();
    }

    /**
     * за период оплачено пени
     * @param Flat $flat
     * @return float
     */
    private function getPeriodPayFineFieldValue(Flat $flat)
    {
        return $flat->getPeriodPayFine();
    }

    /**
     * размер долга
     * @param Flat $flat
     * @return float
     */
    private function getSumDebtFieldValue(Flat $flat)
    {
        return $flat->getSumDebt();
    }

    /**
     * размер пени
     * @param Flat $flat
     * @return float
     */
    private function getSumFineFieldValue(Flat $flat)
    {
        return $flat->getSumFine();
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