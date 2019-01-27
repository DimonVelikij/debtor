<?php

namespace Tests\AppBundle\fixtures;

use AppBundle\Entity\Street;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class StreetFixtures extends Fixture
{
    const TEST_STREET_LENINA_REFERENCE = 'street-lenina';

    public function load(ObjectManager $manager)
    {
        $streetLenina = new Street();
        $streetLenina
            ->setTitle('Ленина')
            ->setSlug('lenina')
            ->setType($this->getReference(StreetTypeFixtures::TEST_STREET_TYPE_STREET_REFERENCE))
            ->setCity($this->getReference(CityFixtures::TEST_CITY_EKATERINBURG_REFERENCE));

        $manager->persist($streetLenina);
        $manager->flush();

        $this->addReference(self::TEST_STREET_LENINA_REFERENCE, $streetLenina);
    }
}
