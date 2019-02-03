<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\OwnershipStatus;

class OwnershipStatusTest extends ValidatorTestCase
{
    public function testCreateOwnershipStatus()
    {
        $ownershipStatus = new OwnershipStatus();
        $this->assertInstanceOf(OwnershipStatus::class, $ownershipStatus);
        $this->assertEquals('Укажите статус собственности', $ownershipStatus->message);
    }
}
