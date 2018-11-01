<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class JudicialSectorValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value) {
            if (count($constraint->types) !== count($value->toArray())) {
                $message = 'Количество судебных участков должно быть: ' . count($constraint->types);

                $this->context->buildViolation($message)
                    ->setParameter('{{ string }}', '')
                    ->addViolation();

                return;
            }

            $types = array_reduce($value->toArray(), function ($acc, \AppBundle\Entity\JudicialSector $judicialSector) {
                if (!in_array($judicialSector->getType(), $acc)) {
                    $acc[] = $judicialSector->getType();
                }
                return $acc;
            }, []);

            $validationResult = array_diff($constraint->types, $types);

            if (count($validationResult)) {
                $addedTypes = [];
                foreach ($validationResult as $type) {
                    $addedTypes[] = \AppBundle\Entity\JudicialSector::$types[$type];
                }

                $message = 'Необходимо еще добавить судебные участки следующих типов: ' . implode(', ', $addedTypes);

                $this->context->buildViolation($message)
                    ->setParameter('{{ string }}', '')
                    ->addViolation();

                return;
            }
        }
    }
}