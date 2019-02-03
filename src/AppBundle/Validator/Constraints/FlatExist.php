<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class FlatExist extends Constraint
{
    public $message = "Помещение №{{ flat }} уже существует в доме №{{ house }} на улице '{{ street }}' в городе '{{ city }}'. Обслуживается управляющей компанией '{{ company }}'";
}