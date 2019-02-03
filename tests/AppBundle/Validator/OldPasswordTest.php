<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Entity\Subscriber;
use AppBundle\Validator\Constraints\OldPassword;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class OldPasswordTest extends ValidatorTestCase
{
    public function testCreateOldPassword()
    {
        try {
            new OldPassword();
        } catch (InvalidArgumentException $exception) {
            $this->assertEquals("Undefined option 'user'", $exception->getMessage());
        }

        try {
            new OldPassword(['user' => new Subscriber()]);
        } catch (InvalidArgumentException $exception) {
            $this->assertEquals("Option 'user' must be 'UserInterface'", $exception->getMessage());
        }

        $user = $this->getEntityManager()->getRepository('AppBundle:User')->findOneBy(['username' => 'admin']);
        $oldPassword = new OldPassword(['user' => $user]);
        $this->assertInstanceOf(OldPassword::class, $oldPassword);
        $this->assertEquals('Неверно указан старый пароль', $oldPassword->message);
        $this->assertEquals($user, $oldPassword->user);
    }
}
