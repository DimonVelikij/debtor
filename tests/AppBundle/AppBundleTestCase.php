<?php

namespace Tests\AppBundle;

use Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Schema\MySqlSchemaManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Базовый класс тестов
 *
 * Class AppBundleTestCase
 * @package Tests\AppBundle
 */
class AppBundleTestCase extends WebTestCase
{
    const DELETE = ORMPurger::PURGE_MODE_DELETE;//удаляем записи
    const TRUNCATE = ORMPurger::PURGE_MODE_TRUNCATE;//выполняем truncate table

    /**
     * контейнер
     * @var ContainerInterface
     */
    private $container;

    /**
     * менеджер доктрины
     * @var EntityManager
     */
    private $entityManager;

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
     * список таблиц, которые пересоздаются перед тестом (truncate table)
     * @var array
     */
    private $truncateTables = [];

    /**
     * получение контейнера
     * @return null|ContainerInterface
     */
    protected function getContainer()
    {
        if (!$this->container) {
            $this->container = static::createClient()->getContainer();
        }

        return $this->container;
    }

    /**
     * получение менеджера доктрины
     * @return EntityManager|object
     */
    protected function getEntityManager()
    {
        if (!$this->entityManager) {
            $this->entityManager = $this->getContainer()->get('doctrine.orm.doctrine_entity_manager');
        }

        return $this->entityManager;
    }

    /**
     * выполяется перед каждым тестом
     */
    protected function setUp()
    {

        parent::setUp();
    }

    /**
     * выполняется после каждого теста
     */
    protected function tearDown()
    {
        if ($this->entityManager) {
            $this->entityManager->close();
            $this->entityManager = null;
        }

        parent::tearDown();
    }

    /**
     * добавление фикстуры
     * @param FixtureInterface $fixture
     */
    protected function addFixture(FixtureInterface $fixture)
    {
        $this->getFixtureLoader()->addFixture($fixture);
    }

    /**
     * добавление списка фикстур
     * @param array $fixtures
     */
    protected function addFixtures(array $fixtures = [])
    {
        for ($i = 0; $i < count($fixtures); $i++) {
            $this->addFixture($fixtures[$i]);
        }
    }

    /**
     * устанавливаем список таблиц, которые должны пересоздаваться перед тестом
     * @param array $truncateTables
     */
    protected function setTruncateTables(array $truncateTables)
    {
        $this->truncateTables = $truncateTables;
    }

    /**
     * загрузка всех фикстур в базу
     * @param int $mode
     * @param bool $append
     */
    protected function executeFixtures($mode = self::DELETE, $append = false)
    {
        $this->getFixtureExecutor($mode)->execute($this->getFixtureLoader()->getFixtures(), $append);
    }

    /**
     * получение исполнителя фикстур
     * @param int $mode
     * @return ORMExecutor
     */
    private function getFixtureExecutor($mode = self::DELETE)//после обновления doctrine fixtures bundle поменять на truncate
    {
        if (!$this->fixtureExecutor) {
            /** @var MySqlSchemaManager $schemaManager */
            $schemaManager = $this->getEntityManager()->getConnection()->getSchemaManager();

            $truncateExcludedTables = [];

            /** @var Table $table */
            foreach ($schemaManager->listTables() as $table) {//определяем таблицы, которые пересоздадутся перед тестом
                if (!in_array($table->getName(), $this->truncateTables)) {
                    $truncateExcludedTables[] = $table->getName();
                }
            }

            $ormPurger = new ORMPurger($this->getEntityManager(), $truncateExcludedTables);
            $ormPurger->setPurgeMode($mode);

            $this->fixtureExecutor = new ORMExecutor($this->getEntityManager(), $ormPurger);
        }

        return $this->fixtureExecutor;
    }

    /**
     * получение загрузчика фикстур
     * @return SymfonyFixturesLoader
     */
    private function getFixtureLoader()
    {
        if (!$this->fixtureLoader) {
            $this->fixtureLoader = new SymfonyFixturesLoader($this->getContainer());
        }

        return $this->fixtureLoader;
    }
}
