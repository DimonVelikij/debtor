<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\CityExist;
use Tests\AppBundle\AppBundleTestCase;

class CityExistTest extends AppBundleTestCase
{
    public function testCreateCityExist()
    {
        $cityExistConstraints = new CityExist();
        $this->assertInstanceOf(CityExist::class, $cityExistConstraints);
    }
}
