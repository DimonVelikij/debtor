<?php

namespace Tests\AppBundle;

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
    use FixturesTrait;

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
}
