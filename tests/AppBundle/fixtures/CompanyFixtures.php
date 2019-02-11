<?php

namespace Tests\AppBundle\fixtures;

use AppBundle\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CompanyFixtures extends Fixture
{
    const TEST_COMPANY_ONE_REFERENCE = 'company-one';
    const TEST_COMPANY_TWO_REFERENCE = 'company-two';

    public function load(ObjectManager $manager)
    {
        $companyOne = new Company();
        $companyOne
            ->setTitle('Управляющая компания 1')
            ->setAddress('Екатеринбург, ул. Московская, д.1')
            ->setBankName('БИН Банк')
            ->setBik('БИК УК1')
            ->setCheckingAccount('Рачетный счет УК1')
            ->setCorrespondentAccount('Корреспондентский счет УК1')
            ->setEmail('uk1@mail.ru')
            ->setInn('ИНН УК1')
            ->setOgrn('ОГРН УК1')
            ->setPhone('89223334455')
            ->setPostAddress('Екатеринбург, ул. Московская, д.1')
            ->setDirectorName('Иванов Иван Иванович')
            ->setDirectorDocument('Акт')
            ->setDirectorPosition('Директор');

        $manager->persist($companyOne);

        $companyTwo = new Company();
        $companyTwo
            ->setTitle('Управляющая компания 2')
            ->setAddress('Екатеринбург, ул. Московская, д.2')
            ->setBankName('БИН Банк')
            ->setBik('БИК УК2')
            ->setCheckingAccount('Рачетный счет УК2')
            ->setCorrespondentAccount('Корреспондентский счет УК2')
            ->setEmail('uk2@mail.ru')
            ->setInn('ИНН УК2')
            ->setOgrn('ОГРН УК2')
            ->setPhone('89223334455')
            ->setPostAddress('Екатеринбург, ул. Московская, д.2')
            ->setDirectorName('Петров Петр Петрович')
            ->setDirectorDocument('Акт')
            ->setDirectorPosition('Директор');

        $manager->persist($companyTwo);

        $manager->flush();

        $this->addReference(self::TEST_COMPANY_ONE_REFERENCE, $companyOne);
        $this->addReference(self::TEST_COMPANY_TWO_REFERENCE, $companyTwo);
    }
}
