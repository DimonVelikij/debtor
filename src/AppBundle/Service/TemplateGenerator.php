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
        'company_name',
        'company_address'
    ];

    /** @var array названия полей подстановки в шаблон */
    private static $templateFieldTitles = [
        'Город',
        'Улица',
        'Номер дома',
        'Номер помещения',
        'Название управляющей компании',
        'Адрес управляющей компании'
    ];

    /** @var array какую сущность использовать для получения значения поля подстановки */
    private static $templateFieldValueEntity = [
        'city'              => 'flat',
        'street'            => 'flat',
        'house_number'      => 'flat',
        'flat_number'       => 'flat',
        'company_name'      => 'flat',
        'company_address'   => 'flat'
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

                $pdfDir = '/pdf/' . $flat->getId() . '/' . $event->getAlias() . '_' . md5(uniqid()) . '.pdf';

                //генерация pdf
                /*$this->pdfGenerator->generateFromHtml(
                    $this->wrapUpTemplate($template),
                    $this->rootDir . $pdfDir
                );*/

                $pdfLinks[] = $pdfDir;
            }
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
     * получение названия города
     * @param Flat $flat
     * @return string
     */
    private function getCityFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getStreet()->getCity()->getTitle();
    }

    /**
     * получение названия улицы
     * @param Flat $flat
     * @return string
     */
    private function getStreetFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getStreet()->getTitle();
    }

    /**
     * получение номера дома
     * @param Flat $flat
     * @return string
     */
    private function getHouseNumberFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getNumber();
    }

    /**
     * получение номера помещения
     * @param Flat $flat
     * @return string
     */
    private function getFlatNumberFieldValue(Flat $flat)
    {
        return $flat->getNumber();
    }

    /**
     * получение названия управляющей компании
     * @param Flat $flat
     * @return string
     */
    private function getCompanyNameFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getCompany()->getTitle();
    }

    /**
     * получение адреса управляющей компании
     * @param Flat $flat
     * @return string
     */
    private function getCompanyAddressFieldValue(Flat $flat)
    {
        return $flat->getHouse()->getCompany()->getAddress();
    }
}