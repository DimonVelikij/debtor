<?php

namespace Tests\AppBundle\fixtures;

use Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Трейт для работы с фиксутрами
 * Trait FixturesTrait
 * @package Tests\AppBundle
 */
trait FixturesTrait
{
    /**
     * исполнитель фикстур
     * @var ORMExecutor
     */
    private $fixtureExecutor;

    /**
     * загрузчик фикстур
     * @var SymfonyFixturesLoader
     */
    private $fixtureLoader;

    /**
     * добавление фикстуры
     *
     * @param FixtureInterface $fixture
     */
    protected function addFixture(FixtureInterface $fixture)
    {
        $this->getFixtureLoader()->addFixture($fixture);
    }

    /**
     * добавление списка фикстур
     *
     * @param array $fixtures
     */
    protected function addFixtures(array $fixtures = [])
    {
        for ($i = 0; $i < count($fixtures); $i++) {
            $this->addFixture($fixtures[$i]);
        }
    }

    /**
     * загрузка всех фикстур в базу
     *
     * @param bool $append
     */
    protected function executeFixtures($append = false)
    {
        $this->getFixtureExecutor()->execute($this->getFixtureLoader()->getFixtures(), $append);
    }

    /**
     * получение иполнителя фикстур
     *
     * @return ORMExecutor
     */
    private function getFixtureExecutor()
    {
        if (!$this->fixtureExecutor) {
            $ormPurger = new ORMPurger($this->getEntityManager());
            $ormPurger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);

            $this->fixtureExecutor = new ORMExecutor($this->getEntityManager(), $ormPurger);
        }

        return $this->fixtureExecutor;
    }

    /**
     * получение загрузчика фикстур
     *
     * @return SymfonyFixturesLoader
     */
    private function getFixtureLoader()
    {
        if (!$this->fixtureLoader) {
            $this->fixtureLoader = new SymfonyFixturesLoader($this->getContainer());
        }
        return $this->fixtureLoader;
    }

    /**
     * получение контейнера
     *
     * @return ContainerInterface
     */
    abstract protected function getContainer();

    /**
     * получение менедаржера доктрины
     *
     * @return EntityManager
     */
    abstract protected function getEntityManager();
}
