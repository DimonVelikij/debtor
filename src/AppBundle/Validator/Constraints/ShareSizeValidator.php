<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
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
        if (!$constraint instanceof ShareSize) {
            throw new UnexpectedTypeException($constraint, ShareSize::class);
        }

        if (!$value) {
            return;
        }

        if (!preg_match('/^\d+\/\d+$/', $value)) {
            $this->context->buildViolation("Неверно указан размер доли")
                ->addViolation();

            return;
        }

        list($numerator, $denominator) = explode('/' , $value);

        if ((int)$numerator > (int)$denominator) {
            $this->context->buildViolation("Числитель должен быть меньше знаменателя")
                ->addViolation();

            return;
        }
    }
}
