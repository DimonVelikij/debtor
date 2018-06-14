<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Street;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

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
        if ($value) {
            /** @var Street $street */
            $street = $this->context->getObject()->getParent()->getData();
            $searchStreet = $this->entityManager->getRepository('AppBundle:Street')
                ->createQueryBuilder('street')
                ->where('street.title = :street')
                ->innerJoin('street.city', 'city')
                ->andWhere('city.title = :city')
                ->setParameters(['street' => $street->getTitle(), 'city' => $street->getCity()->getTitle()])
                ->getQuery()
                ->getOneOrNullResult();

            if ($searchStreet && $searchStreet->getId() != $street->getId()) {
                $this->context->buildViolation("Улица '{$searchStreet->getTitle()}' уже существует в городе '{$searchStreet->getCity()->getTitle()}'")
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();

                return;
            }
        }
    }
}