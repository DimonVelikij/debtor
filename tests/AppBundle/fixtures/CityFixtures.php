<?php

namespace Tests\AppBundle\fixtures;

use AppBundle\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $city = new City();
        $city
            ->setTitle('Невьянск')
            ->setSlug('nevjansk')
            ->setCityIndex(1234);

        $manager->persist($city);
        $manager->flush();
    }
}
