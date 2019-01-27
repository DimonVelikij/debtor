<?php

namespace Tests\AppBundle\fixtures;

use AppBundle\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    const TEST_CITY_EKATERINBURG_REFERENCE = 'city-ekaterinburg';

    public function load(ObjectManager $manager)
    {
        $cityEkaterinburg = new City();
        $cityEkaterinburg
            ->setTitle('Екатеринбург')
            ->setSlug('ekaterinburg')
            ->setCityIndex(111111);

        $manager->persist($cityEkaterinburg);
        $manager->flush();

        $this->addReference(self::TEST_CITY_EKATERINBURG_REFERENCE, $cityEkaterinburg);
    }
}
