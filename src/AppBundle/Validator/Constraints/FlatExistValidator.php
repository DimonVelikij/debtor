<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Flat;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

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
        if ($value) {
            /** @var Flat $flat */
            $flat = $this->context->getObject()->getParent()->getData();
            $searchFlat = $this->entityManager->getRepository('AppBundle:Flat')
                ->createQueryBuilder('flat')
                ->where('flat.number = :flat_number')
                ->innerJoin('flat.house', 'house')
                ->andWhere('house.number = :house_number')
                ->innerJoin('house.street', 'street')
                ->andWhere('street.title = :street')
                ->innerJoin('street.city', 'city')
                ->andWhere('city.title = :city')
                ->setParameters(['flat_number' => $flat->getNumber(), 'house_number' => $flat->getHouse()->getNumber(), 'street' => $flat->getHouse()->getStreet()->getTitle(), 'city' => $flat->getHouse()->getStreet()->getCity()->getTitle()])
                ->getQuery()
                ->getOneOrNullResult();

            if ($searchFlat && $searchFlat->getId() != $flat->getId()) {
                $this->context->buildViolation("Помещение №{$searchFlat->getNumber()} уже существует в доме №{$searchFlat->getHouse()->getNumber()} на улице '{$searchFlat->getHouse()->getStreet()->getTitle()}' в городе '{$searchFlat->getHouse()->getStreet()->getCity()->getTitle()}'. Обслуживается управляющей компанией '{$searchFlat->getHouse()->getCompany()->getTitle()}'")
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();

                return;
            }
        }
    }
}