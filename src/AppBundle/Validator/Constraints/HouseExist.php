<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class HouseExist extends Constraint
{
    public $message = "Дом №{{ house }} уже существует на улице '{{ street }}' в городе '{{ city }}'. Обслуживается управляющей компанией '{{ company }}'";
}