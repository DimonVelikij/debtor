<?php

namespace AppBundle\Service;

use AppBundle\Entity\Debtor;
use AppBundle\Entity\Event;
use AppBundle\Entity\Flat;
use AppBundle\Entity\FlatEvent;
use AppBundle\Entity\Subscriber;
use AppBundle\Exception\NoProcessException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\QueryBuilder;
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
     * получение списка помещений для обработки
     * помещения без ошибок генерации и у которых шаблон не финальный
     * @return array
     */
    public function getProcessFlats()
    {
        /** @var QueryBuilder $flatQueryBuilder */
        $flatQueryBuilder = $this->em
            ->getRepository('AppBundle:Flat')
            ->createQueryBuilder('flat');

        return $flatQueryBuilder
            ->where('flat.isGenerateErrors = :isGenerateErrors')
            ->andWhere('flat.archive = :isArchive')
            ->setParameters([
                'isGenerateErrors'  =>  false,
                'isArchive'         =>  false
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * получение стартового события
     * @return Event|null
     */
    public function getStartEvent()
    {
        return $this->em
            ->getRepository('AppBundle:Event')
            ->createQueryBuilder('event')
            ->where('event.isStart = :isStart')
            ->setParameter('isStart', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function generateTemplate(Flat $flat, FlatEvent $flatEvent)
    {
        if (!$this->isProcessEvent($flatEvent)) {
            throw new NoProcessException();
        }
    }

    /**
     * генерация pdf документа
     * @param Flat $flat
     * @return array
     */
//    public function generateTemplate(Flat $flat)
//    {
//        //если судебный шаблон - работаем с должниками, иначе с абонентами
//        $subjects = $flat->getTemplate()->getIsJudicial() ? $flat->getDebtors() : $flat->getSubscribers();
//
//        $pdfLinks = [];
//
//        /** @var Debtor|Subscriber $subject */
//        foreach ($subjects as $subject) {
//            /** @var string $template */
//            $template = $flat->getTemplate()->getTemplate();
//            /** @var string $field */
//            foreach ($flat->getTemplate()->getTemplateFields() as $field) {
//                $fieldValueMethod = $this->getFieldValueMethod($field);
//                //заменяем поля подстановки на реальные данные
//                $template = $this->templateReplace(
//                    '{{' . $field . '}}',
//                    $this->$fieldValueMethod(TemplateGenerator::$templateFieldValueEntity[$field] === 'flat' ? $flat : $subject),
//                    $template
//                );
//            }
//
//            $pdfDir = '/pdf/' . $flat->getId() . '/' . $flat->getTemplate()->getSlug() . '_' . md5(uniqid()) . '.pdf';
//
//            //генерация pdf
//            $this->pdfGenerator->generateFromHtml(
//                $this->wrapUpTemplate($template),
//                $this->rootDir . $pdfDir
//            );
//
//            $pdfLinks[] = $pdfDir;
//        }
//
//        return $pdfLinks;
//    }

    private function isProcessEvent(FlatEvent $flatEvent)
    {

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