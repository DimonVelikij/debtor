<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\OldPassword;
use AppBundle\Validator\Constraints\OldPasswordValidator;

class OldPasswordValidatorTest extends ValidatorTestCase
{
    private $securityPasswordEncoder;

    protected function setUp()
    {
        parent::setUp();
        $this->securityPasswordEncoder = $this->getContainer()->get('security.password_encoder');
    }

    /**
     * старый пароль введен верно
     */
    public function testValidate()
    {
        $user = $this->getEntityManager()->getRepository('AppBundle:User')->findOneBy(['username' => 'admin']);
        $oldPasswordConstraint = new OldPassword(['user' => $user]);
        $oldPasswordValidator = new OldPasswordValidator($this->securityPasswordEncoder);

        $context = $this->getExecutionContextOkMock();
        $oldPasswordValidator->initialize($context);

        $oldPasswordValidator->validate('1111', $oldPasswordConstraint);
    }

    /**
     * старый пароль введен неверно
     */
    public function testInvalidate()
    {
        $user = $this->getEntityManager()->getRepository('AppBundle:User')->findOneBy(['username' => 'admin']);
        $oldPasswordConstraint = new OldPassword(['user' => $user]);
        $oldPasswordValidator = new OldPasswordValidator($this->securityPasswordEncoder);

        $context = $this->getExecutionContextErrorMock($oldPasswordConstraint->message, ['']);
        $oldPasswordValidator->initialize($context);

        $oldPasswordValidator->validate('11111', $oldPasswordConstraint);
    }
}
