<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\CityExist;

class CityExistTest extends ValidatorTestCase
{
    public function testCreateCityExist()
    {
        $cityExistConstraints = new CityExist();
        $this->assertInstanceOf(CityExist::class, $cityExistConstraints);
        $this->assertEquals("Город '{{ city }}' уже существует", $cityExistConstraints->message);
    }
}
