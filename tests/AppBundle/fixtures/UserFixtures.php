<?php

namespace Tests\AppBundle\fixtures;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $userSuperAdmin = new User();
        $userSuperAdmin
            ->setUsername('admin')
            ->setUsernameCanonical('admin')
            ->setEmail('admin@mail.ru')
            ->setEmailCanonical('admin@mail.ru')
            ->setEnabled(true)
            ->setSalt(null)
            ->setPassword('$2y$13$yp09M7.x.H3XJxgJb4jzP.WJF1gAtNEOqvs/TeqQQSNzEjH26/tO6')
            ->setLastLogin(new \DateTime())
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setCompany($this->getReference(CompanyFixtures::TEST_COMPANY_ONE_REFERENCE));

        $manager->persist($userSuperAdmin);

        $userAdmin = new User();
        $userAdmin
            ->setUsername('user')
            ->setUsernameCanonical('user')
            ->setEmail('user@mail.ru')
            ->setEmailCanonical('user@mail.ru')
            ->setEnabled(true)
            ->setSalt(null)
            ->setPassword('$2y$13$yp09M7.x.H3XJxgJb4jzP.WJF1gAtNEOqvs/TeqQQSNzEjH26/tO6')
            ->setLastLogin(new \DateTime())
            ->setRoles(['ROLE_ADMIN'])
            ->setCompany($this->getReference(CompanyFixtures::TEST_COMPANY_TWO_REFERENCE));

        $manager->persist($userAdmin);

        $manager->flush();
    }
}
