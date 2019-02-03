<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\City;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CityExistValidator extends ConstraintValidator
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
        if (!$constraint instanceof CityExist) {
            throw new UnexpectedTypeException($constraint, CityExist::class);
        }

        if (!$value) {
            return;
        }

        /** @var City $city */
        $city = $this->context->getObject()->getParent()->getData();
        $searchCity = $this->entityManager->getRepository('AppBundle:City')->findOneBy(['title' => $value]);

        if ($searchCity && $searchCity->getId() != $city->getId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ city }}', $value)
                ->addViolation();

            return;
        }
    }
}