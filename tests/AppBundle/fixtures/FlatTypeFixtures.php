<?php

namespace Tests\AppBundle\fixtures;

use AppBundle\Entity\FlatType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class FlatTypeFixtures extends Fixture
{
    const TEST_FLAT_TYPE_FLAT_REFERENCE = 'flat-type-flat';

    public function load(ObjectManager $manager)
    {
        $flatTypeFlat = new FlatType();
        $flatTypeFlat
            ->setTitle('Квартира');

        $manager->persist($flatTypeFlat);
        $manager->flush();

        $this->addReference(self::TEST_FLAT_TYPE_FLAT_REFERENCE, $flatTypeFlat);
    }
}
