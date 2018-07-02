<?php

namespace AppBundle\EventGenerator;

class EventType
{
    //алиасы типов событий
    const PRETENSE_ALIAS    = 'pretense';
    const JUDICATURE_ALIAS  = 'judicature';
    const PERFORMANCE_ALIS  = 'performance';

    //названия типов событий
    const PRETENSE_TITLE    = 'Претензия';
    const JUDICATURE_TITLE  = 'Судебный';
    const PERFORMANCE_TITLE = 'Исполнительное производство';

    //типы событий по алиасам
    public static $types = [
        self::PRETENSE_ALIAS    =>  self::PRETENSE_TITLE,
        self::JUDICATURE_ALIAS  =>  self::JUDICATURE_TITLE,
        self::PERFORMANCE_ALIS  =>  self::PERFORMANCE_TITLE
    ];

    //список типов событий для сонаты
    public static $sonataTypeChoice = [
        self::PRETENSE_TITLE    =>  self::PRETENSE_ALIAS,
        self::JUDICATURE_TITLE  =>  self::JUDICATURE_ALIAS,
        self::PERFORMANCE_TITLE =>  self::PERFORMANCE_ALIS
    ];
}