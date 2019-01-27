<?php

namespace Tests\AppBundle\fixtures;

use AppBundle\Entity\StreetType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class StreetTypeFixtures extends Fixture
{
    const TEST_STREET_TYPE_STREET_REFERENCE = 'street-type-street';

    public function load(ObjectManager $manager)
    {
        $streetTypeStreet = new StreetType();
        $streetTypeStreet
            ->setTitle('улица');

        $manager->persist($streetTypeStreet);
        $manager->flush();

        $this->addReference(self::TEST_STREET_TYPE_STREET_REFERENCE, $streetTypeStreet);
    }
}
