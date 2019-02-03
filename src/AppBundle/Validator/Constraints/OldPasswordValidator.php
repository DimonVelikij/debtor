<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OldPasswordValidator extends ConstraintValidator
{
    /** @var UserPasswordEncoder  */
    private $userPasswordEncoder;

    /**
     * OldPasswordValidator constructor.
     * @param UserPasswordEncoder $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoder $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @param string $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof OldPassword) {
            throw new UnexpectedTypeException($constraint, OldPassword::class);
        }

        if ($value && !$this->userPasswordEncoder->isPasswordValid($constraint->user, $value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }
    }
}