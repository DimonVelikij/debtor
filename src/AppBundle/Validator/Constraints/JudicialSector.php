<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraint;

class JudicialSector extends Constraint
{
    public $message = "Количество судебных участков должно быть: {{ count }}. Необходимо еще добавить судебные участки следующих типов: {{ types }}";

    public $types = [];

    public function __construct($options = null)
    {
        if (!isset($options['types'])) {
            throw new InvalidArgumentException("Undefined option 'types'");
        }

        if (!is_array($options['types'])) {
            throw new InvalidArgumentException("Option 'type' must be array");
        }

        $this->types = $options['types'];

        parent::__construct($options);
    }
}