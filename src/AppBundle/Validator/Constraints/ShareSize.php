<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ShareSize extends Constraint
{
    public $message = "Неверно указан размер доли";
    public $numeratorDenominatorMessage = "Числитель должен быть меньше знаменателя";
}
