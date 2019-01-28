<?php

namespace Tests\AppBundle\fixtures;

use AppBundle\Entity\JudicialSector;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class JudicialSectorFixtures extends Fixture
{
    const TEST_JUDICIAL_SECTOR_DISTRICT_REFERENCE = 'judicial-sector-district';
    const TEST_JUDICIAL_SECTOR_MAGISTRATE_REFERENCE = 'judicial-sector-magistrate';
    const TEST_JUDICIAL_SECTOR_ARBITRATION_REFERENCE = 'judicial-sector-arbitration';

    public function load(ObjectManager $manager)
    {
        $judicialSectorDistrict = new JudicialSector();
        $judicialSectorDistrict
            ->setName('Районный суд')
            ->setAddress('Екатеринбург, ул. Куйбышева, д.1')
            ->setRequisites('Реквизиты районного суда')
            ->setType(JudicialSector::DISTRICT);

        $manager->persist($judicialSectorDistrict);

        $judicialSectorMagistrate = new JudicialSector();
        $judicialSectorMagistrate
            ->setName('Мировой суд')
            ->setAddress('Екатеринбург, ул. Куйбышева, д.2')
            ->setRequisites('Реквизиты мирового суда')
            ->setType(JudicialSector::MAGISTRATE);

        $manager->persist($judicialSectorMagistrate);

        $judicialSectorArbitration = new JudicialSector();
        $judicialSectorArbitration
            ->setName('Арбитражный суд')
            ->setAddress('Екатеринбург, ул. Куйбышева, д.3')
            ->setRequisites('Реквизиты арбитражного суда')
            ->setType(JudicialSector::ARBITRATION);

        $manager->persist($judicialSectorArbitration);

        $manager->flush();

        $this->addReference(self::TEST_JUDICIAL_SECTOR_DISTRICT_REFERENCE, $judicialSectorDistrict);
        $this->addReference(self::TEST_JUDICIAL_SECTOR_MAGISTRATE_REFERENCE, $judicialSectorMagistrate);
        $this->addReference(self::TEST_JUDICIAL_SECTOR_ARBITRATION_REFERENCE, $judicialSectorArbitration);
    }
}
