<?php

namespace AppBundle\Service;

use AppBundle\Entity\Flat;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

class TemplateGenerator
{
    const TEMPLATE_FIELD_ALIASES = [
        'company_name',
        'company_address'
    ];

    const TEMPLATE_FIELD_TITLES = [
        'Название управляющей компании',
        'Адрес управляющей компании'
    ];

    /** @var EntityManager  */
    private $em;

    /**
     * TemplateGenerator constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * получение доступных полей для шаблона
     * @return array
     */
    public function getTemplateFields()
    {
        $templateFields = [];

        for ($i = 0; $i < count(TemplateGenerator::TEMPLATE_FIELD_ALIASES); $i++) {
            $templateFields[TemplateGenerator::TEMPLATE_FIELD_TITLES[$i] . ' {{' . TemplateGenerator::TEMPLATE_FIELD_ALIASES[$i] . '}}'] = TemplateGenerator::TEMPLATE_FIELD_ALIASES[$i];
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

    public function generateTemplate(Flat $flat)
    {
        return 'https://yandex.ru';
    }
}