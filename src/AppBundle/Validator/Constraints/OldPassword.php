<?php

namespace AppBundle\Validator\Constraints;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraint;

class OldPassword extends Constraint
{
    public $user = null;
    public $message = 'Неверно указан старый пароль';

    public function __construct($options = null)
    {
        if (!isset($options['user'])) {
            throw new InvalidArgumentException("Undefined option 'user'");
        }

        if (!$options['user'] instanceof UserInterface) {
            throw new InvalidArgumentException("Option 'user' must be 'UserInterface'");
        }

        $this->user = $options['user'];

        parent::__construct($options);
    }
}