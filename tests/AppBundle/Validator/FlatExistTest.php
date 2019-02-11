<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\FlatExist;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class FlatExistTest extends ValidatorTestCase
{
    public function testCreateFlatExist()
    {
        try {
            new FlatExist();
        } catch (InvalidArgumentException $exception) {
            $this->assertEquals("Undefined option 'flatId'", $exception->getMessage());
        }

        $flatExistConstraints = new FlatExist(['flatId' => 1]);
        $this->assertInstanceOf(FlatExist::class, $flatExistConstraints);
        $this->assertEquals("Помещение №{{ flat }} уже существует в доме №{{ house }} на улице '{{ street }}' в городе '{{ city }}'. Обслуживается управляющей компанией '{{ company }}'", $flatExistConstraints->message);
    }
}
