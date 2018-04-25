<?php

namespace AppBundle\Service;

use AppBundle\Entity\City;
use AppBundle\Entity\Flat;
use AppBundle\Entity\House;
use AppBundle\Entity\Street;
use Doctrine\ORM\EntityManager;

class AddressBookValidator
{
    /** @var EntityManager  */
    private $em;

    /**
     * AddressBookValidator constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * валидация города
     * @param City $city
     * @return bool|string
     */
    public function validateCity(City $city)
    {
        /** @var City $searchCity */
        $searchCity = $this->em->getRepository('AppBundle:City')->findOneBy(['title' => $city->getTitle()]);

        if ($searchCity && $searchCity->getId() != $city->getId()) {
            return "Город '{$searchCity->getTitle()}' уже существует";
        }

        return false;
    }

    /**
     * валидация улицы
     * @param Street $street
     * @return bool|string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function validateStreet(Street $street)
    {
        /** @var Street $searchStreet */
        $searchStreet = $this->em->getRepository('AppBundle:Street')
            ->createQueryBuilder('street')
            ->where('street.title = :street')
            ->innerJoin('street.city', 'city')
            ->andWhere('city.title = :city')
            ->setParameters(['street' => $street->getTitle(), 'city' => $street->getCity()->getTitle()])
            ->getQuery()
            ->getOneOrNullResult();

        if ($searchStreet && $searchStreet->getId() != $street->getId()) {
            return "Улица '{$searchStreet->getTitle()}' уже существует в городе '{$searchStreet->getCity()->getTitle()}'";
        }

        return false;
    }

    /**
     * валидация дома
     * @param House $house
     * @return bool|string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function validateHouse(House $house)
    {
        /** @var House $searchHouse */
        $searchHouse = $this->em->getRepository('AppBundle:House')
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
            return "Дом №{$searchHouse->getNumber()} уже существует на улице '{$searchHouse->getStreet()->getTitle()}' в городе '{$searchHouse->getStreet()->getCity()->getTitle()}'. Обслуживается управляющей компанией '{$searchHouse->getCompany()->getTitle()}'";
        }

        return false;
    }

    /**
     * валидация помещения
     * @param Flat $flat
     * @return bool|string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function validateFlat(Flat $flat)
    {
        /** @var Flat $searchFlat */
        $searchFlat = $this->em->getRepository('AppBundle:Flat')
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
            return "Помещение №{$searchFlat->getNumber()} уже существует в доме №{$searchFlat->getHouse()->getNumber()} на улице '{$searchFlat->getHouse()->getStreet()->getTitle()}' в городе '{$searchFlat->getHouse()->getStreet()->getCity()->getTitle()}'. Обслуживается управляющей компанией '{$searchFlat->getHouse()->getCompany()->getTitle()}'";
        }

        return false;
    }
}