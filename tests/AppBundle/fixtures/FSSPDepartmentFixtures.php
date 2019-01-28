<?php

namespace Tests\AppBundle\fixtures;

use AppBundle\Entity\FSSPDepartment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class FSSPDepartmentFixtures extends Fixture
{
    const TEST_FSSPDepartment_REFERENCE = 'fssp-department';

    public function load(ObjectManager $manager)
    {
        $FSSPDepartment = new FSSPDepartment();
        $FSSPDepartment
            ->setName('Отделение ФССП')
            ->setAddress('Адрес отделения ФССП');

        $manager->persist($FSSPDepartment);
        $manager->flush();

        $this->addReference(self::TEST_FSSPDepartment_REFERENCE, $FSSPDepartment);
    }
}
