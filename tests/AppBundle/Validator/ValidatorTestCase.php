<?php

namespace Tests\AppBundle\Validator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;
use Tests\AppBundle\AppBundleTestCase;

class ValidatorTestCase extends AppBundleTestCase
{
    /**
     * mock формы
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFormMock()
    {
        return $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * mock формы с получением данных из отправленной формы
     * @param $formData
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFormDataMock($formData)
    {
        $form = $this->getFormMock();
        $form->expects($this->once())
            ->method('getParent')
            ->willReturn($form);
        $form->expects($this->once())
            ->method('getData')
            ->willReturn($formData);

        return $form;
    }

    /**
     * mock билдера ошибок
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getConstraintViolationBuilderMock()
    {
        return $this->getMockBuilder(ConstraintViolationBuilder::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * mock контекста формы
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getExecutionContextMock()
    {
        return $this->getMockBuilder(ExecutionContext::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * mock контекста формы без ошибок
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getExecutionContextOkMock()
    {
        $context = $this->getExecutionContextMock();

        $context->expects($this->never())
            ->method('buildViolation');

        return $context;
    }

    /**
     * mock контекста формы без ошибок с получением данных из формы
     * @param $formData
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getExecutionContextOkWithDataMock($formData)
    {
        $context = $this->getExecutionContextOkMock();

        $context->expects($this->once())
            ->method('getObject')
            ->willReturn($this->getFormDataMock($formData));

        return $context;
    }

    /**
     * mock контекста формы с ошибками
     * @param string $errorMessage
     * @param array $params
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getExecutionContextErrorMock(string $errorMessage, array $params = [])
    {
        $constraintViolationBuilder = $this->getConstraintViolationBuilderMock();

        if (count($params)) {
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
        }

        $constraintViolationBuilder->expects($this->once())
            ->method('addViolation');

        $context = $this->getExecutionContextMock();
        $context->expects($this->once())
            ->method('buildViolation')
            ->with($this->equalTo($errorMessage))
            ->willReturn($constraintViolationBuilder);

        return $context;
    }

    /**
     * mock контекста формы с ошибками с получением данных из формы
     * @param $formData
     * @param string $errorMessage
     * @param array $params
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getExecutionContextErrorWithDataMock($formData, string $errorMessage, array $params = [])
    {
        $context = $this->getExecutionContextErrorMock($errorMessage, $params);

        $context->expects($this->once())
            ->method('getObject')
            ->willReturn($this->getFormDataMock($formData));

        return $context;
    }

    /**
     * @param $className
     * @param array $collectionData
     * @return PersistentCollection
     */
    protected function getPersistentCollectionMock($className, array $collectionData)
    {
        $metaData = $this->getEntityManager()->getClassMetadata($className);
        $collection = new ArrayCollection($collectionData);
        return new PersistentCollection($this->getEntityManager(), $metaData, $collection);
    }
}
