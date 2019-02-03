<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\ShareSize;

class ShareSizeTest extends ValidatorTestCase
{
    public function testCreateShareSize()
    {
        $shareSizeConstraint = new ShareSize();
        $this->assertInstanceOf(ShareSize::class, $shareSizeConstraint);
        $this->assertEquals("Неверно указан размер доли", $shareSizeConstraint->message);
        $this->assertEquals("Числитель должен быть меньше знаменателя", $shareSizeConstraint->numeratorDenominatorMessage);
    }
}
