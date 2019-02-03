<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class CityExist extends Constraint
{
    public $message = "Город '{{ city }}' уже существует";
}