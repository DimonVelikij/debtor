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
            ->setParameters([
                'flat_number'   => $value,
                'house_number'  => $flat->getHouse()->getNumber(),
                'street'        => $flat->getHouse()->getStreet()->getTitle(),
                'city'          => $flat->getHouse()->getStreet()->getCity()->getTitle()
            ])
            ->getQuery()
            ->getOneOrNullResult();

        if ($searchFlat && $searchFlat->getId() != $flat->getId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameters([
                    '{{ flat }}'    =>  $value,
                    '{{ house }}'   =>  $searchFlat->getHouse()->getNumber(),
                    '{{ street }}'  =>  $searchFlat->getHouse()->getStreet()->getTitle(),
                    '{{ city }}'    =>  $searchFlat->getHouse()->getStreet()->getCity()->getTitle(),
                    '{{ company }}' =>  $searchFlat->getHouse()->getCompany()->getTitle()
                ])
                ->addViolation();

            return;
        }
    }
}