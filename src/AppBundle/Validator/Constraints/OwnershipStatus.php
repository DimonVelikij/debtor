<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class OwnershipStatus extends Constraint
{
    public $message = 'Укажите статус собственности';

    public function __construct($options = null)
    {
        parent::__construct($options);
    }
}