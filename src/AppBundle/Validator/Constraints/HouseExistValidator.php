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
        $house = $this->context->getObject()->getParent()->getData();
        $searchHouse = $this->entityManager->getRepository('AppBundle:House')
            ->createQueryBuilder('house')
            ->where('house.number = :house_number')
            ->innerJoin('house.street', 'street')
            ->andWhere('street.title = :street')
            ->innerJoin('street.city', 'city')
            ->andWhere('city.title = :city')
            ->setParameters([
                'house_number'  => $value,
                'street'        => $house->getStreet()->getTitle(),
                'city'          => $house->getStreet()->getCity()->getTitle()
            ])
            ->getQuery()
            ->getOneOrNullResult();

        if ($searchHouse && $searchHouse->getId() != $house->getId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameters([
                    '{{ house }}'   =>  $value,
                    '{{ street }}'  =>  $searchHouse->getStreet()->getTitle(),
                    '{{ city }}'    =>  $searchHouse->getStreet()->getCity()->getTitle(),
                    '{{ company }}' =>  $searchHouse->getCompany()->getTitle()
                ])
                ->addViolation();

            return;
        }
    }
}