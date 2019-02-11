<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\HouseExist;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class HouseExistTest extends ValidatorTestCase
{
    public function testCreateHouseExist()
    {
        try {
            new HouseExist();
        } catch (InvalidArgumentException $exception) {
            $this->assertEquals("Undefined option 'houseId'", $exception->getMessage());
        }
        $houseExistConstraints = new HouseExist(['houseId' => 1]);
        $this->assertInstanceOf(HouseExist::class, $houseExistConstraints);
        $this->assertEquals("Дом №{{ house }} уже существует на улице '{{ street }}' в городе '{{ city }}'. Обслуживается управляющей компанией '{{ company }}'", $houseExistConstraints->message);
    }
}
