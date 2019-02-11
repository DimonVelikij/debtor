<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraint;

class FlatExist extends Constraint
{
    public $message = "Помещение №{{ flat }} уже существует в доме №{{ house }} на улице '{{ street }}' в городе '{{ city }}'. Обслуживается управляющей компанией '{{ company }}'";
    public $flatId = null;

    public function __construct($options = [])
    {
        if (!array_key_exists('flatId', $options)) {
            throw new InvalidArgumentException("Undefined option 'flatId'");
        }

        $this->flatId = $options['flatId'];

        parent::__construct($options);
    }
}