<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Entity\City;
use AppBundle\Entity\Street;
use AppBundle\Validator\Constraints\StreetExist;
use AppBundle\Validator\Constraints\StreetExistValidator;

class StreetExistValidatorTest extends ValidatorTestCase
{
    /**
     * добавление улицы Малышева в Екатеринбург, улицы Малышева в базе не существует
     */
    public function testAddStreetValidate()
    {
        $streetExistConstraint = new StreetExist(['streetId' => null]);
        $streetExistValidator = new StreetExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextOkMock();
        $streetExistValidator->initialize($context);

        $streetExistValidator->validate('Малышева', $streetExistConstraint);
    }

    /**
     * редактирование улицы, меняем Ленина на Малышева, улицы Малышева в баще не существует
     */
    public function testRenameStreetValidate()
    {
        $street = $this->getEntityManager()->getRepository('AppBundle:Street')->findOneBy(['title' => 'Ленина']);
        $streetExistConstraint = new StreetExist(['streetId' => $street->getId()]);
        $streetExistValidator = new StreetExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextOkMock();;
        $streetExistValidator->initialize($context);

        $streetExistValidator->validate('Малышева', $streetExistConstraint);
    }

    /**
     * редактирование улицы Ленина без переименования, улица Ленина в базе существует
     */
    public function testEditStreetValidate()
    {
        $street = $this->getEntityManager()->getRepository('AppBundle:Street')->findOneBy(['title' => 'Ленина']);
        $streetExistConstraint = new StreetExist(['streetId' => $street->getId()]);
        $streetExistValidator = new StreetExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextOkMock();
        $streetExistValidator->initialize($context);

        $streetExistValidator->validate('Ленина', $streetExistConstraint);
    }

    /**
     * добавление улицы Ленина, улица Ленина существует в базе
     */
    public function testAddStreetInvalidate()
    {
        $streetExistConstraint = new StreetExist(['streetId' => null]);
        $streetExistValidator = new StreetExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextErrorMock($streetExistConstraint->message, [
            '{{ city }}' => 'Екатеринбург',
            '{{ street }}' => 'Ленина'
        ]);
        $streetExistValidator->initialize($context);

        $streetExistValidator->validate('Ленина', $streetExistConstraint);
    }
}
