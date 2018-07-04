<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\MKD;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MKDExistValidator extends ConstraintValidator
{
    private $entityManager;

    /**
     * MKDExistValidator constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($value, Constraint $constraint)
    {
        if ($value) {
            /** @var MKD $mkd */
            $mkd = $this->context->getObject()->getParent()->getData();
            $searchMKD = $this->entityManager->getRepository('AppBundle:MKD')
                ->createQueryBuilder('mkd')
                ->where('mkd.houseNumber = :house_number')
                ->innerJoin('mkd.street', 'street')
                ->andWhere('street.title = :street')
                ->innerJoin('street.city', 'city')
                ->andWhere('city.title = :city')
                ->setParameters(['house_number' => $mkd->getHouseNumber(), 'street' => $mkd->getStreet()->getTitle(), 'city' => $mkd->getStreet()->getCity()->getTitle()])
                ->getQuery()
                ->getOneOrNullResult();

            if ($searchMKD && $searchMKD->getId() != $mkd->getId()) {
                $this->context->buildViolation("МКД для дома №{$searchMKD->getHouseNumber()} уже существует")
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();

                return;
            }
        }
    }
}