<?php

namespace Tests\AppBundle\fixtures;

use AppBundle\Entity\FlatType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class FlatTypeFixtures extends Fixture
{
    const TEST_FLAT_TYPE_FLAT_REFERENCE = 'flat-type-flat';
    const TEST_FLAT_TYPE_ROOM_REFERENCE = 'flat-type-room';
    const TEST_FLAT_TYPE_OFFICE_REFERENCE = 'flat-type-office';

    public function load(ObjectManager $manager)
    {
        $flatTypeFlat = new FlatType();
        $flatTypeFlat->setTitle('Квартира');

        $manager->persist($flatTypeFlat);

        $flatTypeRoom = new FlatType();
        $flatTypeRoom->setTitle('Комната');

        $manager->persist($flatTypeRoom);

        $flatTypeOffice = new FlatType();
        $flatTypeOffice->setTitle('Офис');

        $manager->persist($flatTypeOffice);

        $manager->flush();

        $this->addReference(self::TEST_FLAT_TYPE_FLAT_REFERENCE, $flatTypeFlat);
        $this->addReference(self::TEST_FLAT_TYPE_ROOM_REFERENCE, $flatTypeRoom);
        $this->addReference(self::TEST_FLAT_TYPE_OFFICE_REFERENCE, $flatTypeOffice);
    }
}
