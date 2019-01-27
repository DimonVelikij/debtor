<?php

namespace Tests\AppBundle\Validator;

use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;
use Tests\AppBundle\AppBundleTestCase;

class ValidatorTestCase extends AppBundleTestCase
{
    /**
     * @param $formObject
     * @param bool $errorMessage
     * @param array $params
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function configureContextValidator($formObject, $errorMessage = false, $params = [])
    {
        $form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $form->expects($this->once())
            ->method('getParent')
            ->willReturn($form);
        $form->expects($this->once())
            ->method('getData')
            ->willReturn($formObject);

        $context = $this->getMockBuilder(ExecutionContext::class)->disableOriginalConstructor()->getMock();
        $context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($form);

        if ($errorMessage) {
            $constraintViolationBuilder = $this->getMockBuilder(ConstraintViolationBuilder::class)->disableOriginalConstructor()->getMock();
            if (array_keys($params) === range(0, count($params) - 1)) {
                $constraintViolationBuilder->expects($this->any())
                    ->method('setParameter')
                    ->with(...$params)
                    ->willReturn($constraintViolationBuilder);
            } else {
                $constraintViolationBuilder->expects($this->any())
                    ->method('setParameters')
                    ->with($params)
                    ->willReturn($constraintViolationBuilder);
            }
            $constraintViolationBuilder->expects($this->once())
                ->method('addViolation');

            $context->expects($this->once())
                ->method('buildViolation')
                ->with($this->equalTo($errorMessage))
                ->willReturn($constraintViolationBuilder);
        } else {
            $context->expects($this->never())
                ->method('buildViolation');
        }

        return $context;
    }
}
