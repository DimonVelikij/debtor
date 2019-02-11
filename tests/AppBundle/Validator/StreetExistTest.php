<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\StreetExist;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class StreetExistTest extends ValidatorTestCase
{
    public function testCreateStreetExist()
    {
        try {
            new StreetExist();
        } catch (InvalidArgumentException $exception) {
            $this->assertEquals("Undefined option 'streetId'", $exception->getMessage());
        }

        $streetExistConstraints = new StreetExist(['streetId' => 1]);
        $this->assertInstanceOf(StreetExist::class, $streetExistConstraints);
        $this->assertEquals("Улица '{{ street }}' уже существует в городе '{{ city }}'", $streetExistConstraints->message);
    }
}
