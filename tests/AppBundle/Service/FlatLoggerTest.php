<?php

namespace Tests\AppBundle;

use AppBundle\Service\FlatLogger;
use Tests\AppBundle\fixtures\CityFixtures;

class FlatLoggerTest extends AppBundleTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->addFixtures([
            new CityFixtures()
        ]);
        $this->executeFixtures();
    }

    public function testLog()
    {
        $flatLogger = new FlatLogger($this->getEntityManager());
        $flatLogger->log();


    }
}