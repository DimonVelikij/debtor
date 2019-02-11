<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraint;

class CityExist extends Constraint
{
    public $message = "Город '{{ city }}' уже существует";
    public $cityId = null;

    public function __construct($options = [])
    {
        if (!array_key_exists('cityId', $options)) {
            throw new InvalidArgumentException("Undefined option 'cityId'");
        }

        $this->cityId = $options['cityId'];

        parent::__construct($options);
    }
}