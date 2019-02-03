<?php

namespace AppBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OwnershipStatusValidator extends ConstraintValidator
{
    /** @var EntityManager  */
    private $em;

    /**
     * OwnershipStatusValidator constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof OwnershipStatus) {
            throw new UnexpectedTypeException($constraint, OwnershipStatus::class);
        }

        if (!$value) {
            return;
        }

        if (count($value['children'])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('', '')
                ->addViolation();

            return;
        }
    }
}