<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Street;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class StreetExistValidator extends ConstraintValidator
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
        if (!$constraint instanceof StreetExist) {
            throw new UnexpectedTypeException($constraint, StreetExist::class);
        }

        if (!$value) {
            return;
        }

        /** @var Street $street */
        $street = $this->context->getObject()->getParent()->getData();
        $searchStreet = $this->entityManager->getRepository('AppBundle:Street')
            ->createQueryBuilder('street')
            ->where('street.title = :street')
            ->innerJoin('street.city', 'city')
            ->andWhere('city.title = :city')
            ->setParameters([
                'street'    => $value,
                'city'      => $street->getCity()->getTitle()
            ])
            ->getQuery()
            ->getOneOrNullResult();

        if ($searchStreet && $searchStreet->getId() != $street->getId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameters([
                    '{{ city }}'    =>  $searchStreet->getCity()->getTitle(),
                    '{{ street }}'  =>  $value
                ])
                ->addViolation();

            return;
        }
    }
}