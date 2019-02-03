<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\FlatExist;

class FlatExistTest extends ValidatorTestCase
{
    public function testCreateFlatExist()
    {
        $flatExistConstraints = new FlatExist();
        $this->assertInstanceOf(FlatExist::class, $flatExistConstraints);
        $this->assertEquals("Помещение №{{ flat }} уже существует в доме №{{ house }} на улице '{{ street }}' в городе '{{ city }}'. Обслуживается управляющей компанией '{{ company }}'", $flatExistConstraints->message);
    }
}
