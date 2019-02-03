<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\OwnershipStatus;
use AppBundle\Validator\Constraints\OwnershipStatusValidator;

class OwnershipStatusValidatorTest extends ValidatorTestCase
{
    /**
     * нет дочерних статусов
     */
    public function testOwnershipStatusValidate()
    {
        $ownershipStatusConstraint = new OwnershipStatus();
        $ownershipStatusValidator = new OwnershipStatusValidator($this->getEntityManager());

        $context = $this->getExecutionContextOkMock();
        $ownershipStatusValidator->initialize($context);

        $ownershipStatusValidator->validate(['children' => []], $ownershipStatusConstraint);
    }

    /**
     * есть дочерние статусы
     */
    public function testOwnershipStatusInvalidate()
    {
        $ownershipStatusConstraint = new OwnershipStatus();
        $ownershipStatusValidator = new OwnershipStatusValidator($this->getEntityManager());

        $context = $this->getExecutionContextErrorMock($ownershipStatusConstraint->message, ['']);
        $ownershipStatusValidator->initialize($context);

        $ownershipStatusValidator->validate(['children' => ['sub_status' => 'sub_status']], $ownershipStatusConstraint);
    }
}
