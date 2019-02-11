<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\CityExist;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class CityExistTest extends ValidatorTestCase
{
    public function testCreateCityExist()
    {
        try {
            new CityExist();
        } catch (InvalidArgumentException $exception) {
            $this->assertEquals("Undefined option 'cityId'", $exception->getMessage());
        }

        $cityExistConstraints = new CityExist(['cityId' => 1]);
        $this->assertInstanceOf(CityExist::class, $cityExistConstraints);
        $this->assertEquals("Город '{{ city }}' уже существует", $cityExistConstraints->message);
    }
}
