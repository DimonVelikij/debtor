<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\City;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ShareSizeValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value) {
            if (!preg_match('/^\d+\/\d+$/', $value)) {
                $this->context->buildViolation("Неверно указан размер доли")
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();

                return;
            }

            list($numerator, $denominator) = explode('/' , $value);

            if ((int)$numerator > (int)$denominator) {
                $this->context->buildViolation("Числитель должен быть меньше знаменателя")
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();

                return;
            }
        }
    }
}