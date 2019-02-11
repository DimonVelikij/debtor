<?php

namespace Tests\AppBundle\fixtures;

use AppBundle\Entity\House;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class HouseFixtures extends Fixture
{
    const TEST_HOUSE_ONE_REFERENCE = 'house-one';
    const TEST_HOUSE_TWO_REFERENCE = 'house-two';

    public function load(ObjectManager $manager)
    {
        $houseOne = new House();
        $houseOne
            ->setNumber(1)
            ->setManagementStartDate(new \DateTime('01.01.2000'))
            ->setManagementEndDate(new \DateTime('01.01.2020'))
            ->setLegalDocumentDate(new \DateTime('01.01.2000'))
            ->setLegalDocumentName('Документ на право управления домом 1')
            ->setLegalDocumentNumber('12345678')
            ->setStreet($this->getReference(StreetFixtures::TEST_STREET_LENINA_REFERENCE))
            ->setFsspDepartment($this->getReference(FSSPDepartmentFixtures::TEST_FSSPDepartment_REFERENCE))
            ->setCompany($this->getReference(CompanyFixtures::TEST_COMPANY_ONE_REFERENCE))
            ->addJudicialSector($this->getReference(JudicialSectorFixtures::TEST_JUDICIAL_SECTOR_DISTRICT_REFERENCE))
            ->addJudicialSector($this->getReference(JudicialSectorFixtures::TEST_JUDICIAL_SECTOR_MAGISTRATE_REFERENCE))
            ->addJudicialSector($this->getReference(JudicialSectorFixtures::TEST_JUDICIAL_SECTOR_ARBITRATION_REFERENCE));

        $manager->persist($houseOne);

        $houseTwo = new House();
        $houseTwo
            ->setNumber(2)
            ->setManagementStartDate(new \DateTime('01.01.2000'))
            ->setManagementEndDate(new \DateTime('01.01.2020'))
            ->setLegalDocumentDate(new \DateTime('01.01.2000'))
            ->setLegalDocumentName('Документ на право управления домом 1')
            ->setLegalDocumentNumber('12345678')
            ->setStreet($this->getReference(StreetFixtures::TEST_STREET_LENINA_REFERENCE))
            ->setFsspDepartment($this->getReference(FSSPDepartmentFixtures::TEST_FSSPDepartment_REFERENCE))
            ->setCompany($this->getReference(CompanyFixtures::TEST_COMPANY_TWO_REFERENCE))
            ->addJudicialSector($this->getReference(JudicialSectorFixtures::TEST_JUDICIAL_SECTOR_DISTRICT_REFERENCE))
            ->addJudicialSector($this->getReference(JudicialSectorFixtures::TEST_JUDICIAL_SECTOR_MAGISTRATE_REFERENCE))
            ->addJudicialSector($this->getReference(JudicialSectorFixtures::TEST_JUDICIAL_SECTOR_ARBITRATION_REFERENCE));

        $manager->persist($houseTwo);

        $manager->flush();

        $this->addReference(self::TEST_HOUSE_ONE_REFERENCE, $houseOne);
        $this->addReference(self::TEST_HOUSE_TWO_REFERENCE, $houseTwo);
    }
}
