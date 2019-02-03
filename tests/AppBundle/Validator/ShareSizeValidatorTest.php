<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\ShareSize;
use AppBundle\Validator\Constraints\ShareSizeValidator;

class ShareSizeValidatorTest extends ValidatorTestCase
{
    /**
     * доля указана правильно
     */
    public function testValidate()
    {
        $shareSizeConstraint = new ShareSize();
        $shareSizeValidator = new ShareSizeValidator();

        $context = $this->getExecutionContextOkMock();
        $shareSizeValidator->initialize($context);

        $shareSizeValidator->validate('1/2', $shareSizeConstraint);
    }

    /**
     * доля указана неправильно
     */
    public function testInvalidate()
    {
        $shareSizeConstraint = new ShareSize();
        $shareSizeValidator = new ShareSizeValidator();

        $context = $this->getExecutionContextErrorMock($shareSizeConstraint->message);
        $shareSizeValidator->initialize($context);

        $shareSizeValidator->validate('1', $shareSizeConstraint);
    }

    /**
     * у доли числитель больше знаменателя
     */
    public function testNumeratorDenominatorMessageInvalidate()
    {
        $shareSizeConstraint = new ShareSize();
        $shareSizeValidator = new ShareSizeValidator();

        $context = $this->getExecutionContextErrorMock($shareSizeConstraint->numeratorDenominatorMessage);
        $shareSizeValidator->initialize($context);

        $shareSizeValidator->validate('3/2', $shareSizeConstraint);
    }
}
