<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraint;

class StreetExist extends Constraint
{
    public $message = "Улица '{{ street }}' уже существует в городе '{{ city }}'";
    public $streetId = null;

    public function __construct($options = [])
    {
        if (!array_key_exists('streetId', $options)) {
            throw new InvalidArgumentException("Undefined option 'streetId'");
        }

        $this->streetId = $options['streetId'];

        parent::__construct($options);
    }
}
