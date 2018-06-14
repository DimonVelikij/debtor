<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\House;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

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
        if ($value) {
            /** @var House $house */
            $house = $this->context->getObject()->getParent()->getData();
            $searchHouse = $this->entityManager->getRepository('AppBundle:House')
                ->createQueryBuilder('house')
                ->where('house.number = :house_number')
                ->innerJoin('house.street', 'street')
                ->andWhere('street.title = :street')
                ->innerJoin('street.city', 'city')
                ->andWhere('city.title = :city')
                ->setParameters(['house_number' => $house->getNumber(), 'street' => $house->getStreet()->getTitle(), 'city' => $house->getStreet()->getCity()->getTitle()])
                ->getQuery()
                ->getOneOrNullResult();

            if ($searchHouse && $searchHouse->getId() != $house->getId()) {
                $this->context->buildViolation("Дом №{$searchHouse->getNumber()} уже существует на улице '{$searchHouse->getStreet()->getTitle()}' в городе '{$searchHouse->getStreet()->getCity()->getTitle()}'. Обслуживается управляющей компанией '{$searchHouse->getCompany()->getTitle()}'")
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();

                return;
            }
        }
    }
}