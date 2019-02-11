<?php

namespace Tests\AppBundle\fixtures;

use AppBundle\Entity\Flat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class FlatFixtures extends Fixture
{
    const TEST_FLAT_ONE_REFERENCE = 'flat-one';
    const TEST_FLAT_TWO_REFERENCE = 'flat-two';

    public function load(ObjectManager $manager)
    {
        $flatOne = new Flat();
        $flatOne
            ->setNumber(1)
            ->setArchive(false)
            ->setType($this->getReference(FlatTypeFixtures::TEST_FLAT_TYPE_FLAT_REFERENCE))
            ->setHouse($this->getReference(HouseFixtures::TEST_HOUSE_ONE_REFERENCE));

        $manager->persist($flatOne);

        $flatTwo = new Flat();
        $flatTwo
            ->setNumber(2)
            ->setArchive(false)
            ->setType($this->getReference(FlatTypeFixtures::TEST_FLAT_TYPE_FLAT_REFERENCE))
            ->setHouse($this->getReference(HouseFixtures::TEST_HOUSE_TWO_REFERENCE));

        $manager->persist($flatTwo);

        $manager->flush();

        $this->addReference(self::TEST_FLAT_ONE_REFERENCE, $flatOne);
        $this->addReference(self::TEST_FLAT_TWO_REFERENCE, $flatTwo);
    }
}
