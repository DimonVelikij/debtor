<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraint;

class HouseExist extends Constraint
{
    public $message = "Дом №{{ house }} уже существует на улице '{{ street }}' в городе '{{ city }}'. Обслуживается управляющей компанией '{{ company }}'";
    public $houseId = null;

    public function __construct($options = [])
    {
        if (!array_key_exists('houseId', $options)) {
            throw new InvalidArgumentException("Undefined option 'houseId'");
        }

        $this->houseId = $options['houseId'];

        parent::__construct($options);
    }
}