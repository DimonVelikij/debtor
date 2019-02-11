<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Flat;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class FlatExistValidator extends ConstraintValidator
{
    /** @var EntityManager  */
    private $entityManager;

    /**
     * CityExistValidator constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof FlatExist) {
            throw new UnexpectedTypeException($constraint, HouseExist::class);
        }

        if (!$value) {
            return;
        }

        /** @var Flat $flat */
        $flat = $this->entityManager->getRepository('AppBundle:Flat')->findOneBy(['number' => $value]);

        if ($flat && $constraint->flatId != $flat->getId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameters([
                    '{{ flat }}'    =>  $value,
                    '{{ house }}'   =>  $flat->getHouse()->getNumber(),
                    '{{ street }}'  =>  $flat->getHouse()->getStreet()->getTitle(),
                    '{{ city }}'    =>  $flat->getHouse()->getStreet()->getCity()->getTitle(),
                    '{{ company }}' =>  $flat->getHouse()->getCompany()->getTitle()
                ])
                ->addViolation();

            return;
        }
    }
}