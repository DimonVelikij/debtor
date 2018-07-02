<?php

namespace AppBundle\Exception;

/**
 * Class NotEventTemplate
 * @package AppBundle\Exception
 */
class NoTemplateEventException extends \Exception
{
    //отсутствует шаблон у события, у которого он должен обязательно быть
}