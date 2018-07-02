<?php

namespace AppBundle\Exception;

class NoTemplateFieldsEventException extends \Exception
{
    //отсутствуют поля подстановки в шаблон у события, у которого обязательно должен быть шаблон
}