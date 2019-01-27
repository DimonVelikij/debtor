<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class StreetExist extends Constraint
{
    public $message = "Улица '{{ street }}' уже существует в городе '{{ city }}'";
}
