<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\HouseExist;

class HouseExistTest extends ValidatorTestCase
{
    public function testCreateHouseExist()
    {
        $houseExistConstraints = new HouseExist();
        $this->assertInstanceOf(HouseExist::class, $houseExistConstraints);
        $this->assertEquals("Дом №{{ house }} уже существует на улице '{{ street }}' в городе '{{ city }}'. Обслуживается управляющей компанией '{{ company }}'", $houseExistConstraints->message);
    }
}
