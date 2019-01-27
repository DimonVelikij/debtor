<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\StreetExist;

class StreetExistTest extends ValidatorTestCase
{
    public function testCreateStreetExist()
    {
        $streetExistConstraints = new StreetExist();
        $this->assertInstanceOf(StreetExist::class, $streetExistConstraints);
        $this->assertEquals("Улица '{{ street }}' уже существует в городе '{{ city }}'", $streetExistConstraints->message);
    }
}
