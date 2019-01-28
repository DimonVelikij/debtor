<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class JudicialSectorValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof JudicialSector) {
            throw new UnexpectedTypeException($constraint, JudicialSector::class);
        }

        if (!$value) {
            return;
        }

        if (count($constraint->types) !== count($value->toArray())) {
            $types = array_reduce($value->toArray(), function ($acc, \AppBundle\Entity\JudicialSector $judicialSector) {
                if (!in_array($judicialSector->getType(), $acc)) {
                    $acc[] = $judicialSector->getType();
                }
                return $acc;
            }, []);

            $validationResult = array_diff($constraint->types, $types);

            $addedTypes = [];
            foreach ($validationResult as $type) {
                $addedTypes[] = \AppBundle\Entity\JudicialSector::$types[$type];
            }

            $this->context->buildViolation($constraint->message)
                ->setParameters([
                    '{{ count }}'   =>  count($constraint->types),
                    '{{ types }}'   =>  implode(', ', $addedTypes)
                ])
                ->addViolation();

            return;
        }
    }
}