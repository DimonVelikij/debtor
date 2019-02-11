<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\House;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class HouseExistValidator extends ConstraintValidator
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
        if (!$constraint instanceof HouseExist) {
            throw new UnexpectedTypeException($constraint, HouseExist::class);
        }

        if (!$value) {
            return;
        }

        /** @var House $house */
        $house = $this->entityManager->getRepository('AppBundle:House')->findOneBy(['number' => $value]);

        if ($house && $constraint->houseId != $house->getId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameters([
                    '{{ house }}'   =>  $value,
                    '{{ street }}'  =>  $house->getStreet()->getTitle(),
                    '{{ city }}'    =>  $house->getStreet()->getCity()->getTitle(),
                    '{{ company }}' =>  $house->getCompany()->getTitle()
                ])
                ->addViolation();

            return;
        }
    }
}