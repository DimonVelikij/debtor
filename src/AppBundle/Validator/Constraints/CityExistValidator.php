<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\City;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

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
        if ($value) {
            /** @var City $city */
            $city = $this->context->getObject()->getParent()->getData();
            $searchCity = $this->entityManager->getRepository('AppBundle:City')->findOneBy(['title' => $value]);

            if ($searchCity && $searchCity->getId() != $city->getId()) {
                $this->context->buildViolation("Город '{$searchCity->getTitle()}' уже существует")
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();

                return;
            }
        }
    }
}