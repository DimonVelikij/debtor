<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Entity\City;
use AppBundle\Entity\House;
use AppBundle\Entity\Street;
use AppBundle\Validator\Constraints\HouseExist;
use AppBundle\Validator\Constraints\HouseExistValidator;

class HouseExistValidatorTest extends ValidatorTestCase
{
    /**
     * добаление дома №200 на улицу Ленина города Екатеринбурга, дома №200 в базе не существует
     */
    public function testAddHouseValidate()
    {
        $houseExistConstraint = new HouseExist(['houseId' => null]);
        $houseExistValidator = new HouseExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextOkMock();
        $houseExistValidator->initialize($context);

        $houseExistValidator->validate(200, $houseExistConstraint);
    }

    /**
     * редактирование дома, меняем №1 на №200, №200 в базе не существует
     */
    public function testRenameHouseValidate()
    {
        $house = $this->getEntityManager()->getRepository('AppBundle:House')->findOneBy(['number' => '1']);
        $houseExistConstraint = new HouseExist(['houseId' => $house->getId()]);
        $houseExistValidator = new HouseExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextOkMock();
        $houseExistValidator->initialize($context);

        $houseExistValidator->validate(200, $houseExistConstraint);
    }

    /**
     * редактирование №1 без переименования, №1 в базе существует
     */
    public function testEditHouseValidate()
    {
        $house = $this->getEntityManager()->getRepository('AppBundle:House')->findOneBy(['number' => '1']);
        $houseExistConstraint = new HouseExist(['houseId' => $house->getId()]);
        $houseExistValidator = new HouseExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextOkMock();
        $houseExistValidator->initialize($context);

        $houseExistValidator->validate(1, $houseExistConstraint);
    }

    /**
     * добавление №1 на улицу Ленина города Екатеринбург, дом №1 в базе уже существует
     */
    public function testAddHouseInvalidate()
    {
        $houseExistConstraint = new HouseExist(['houseId' => null]);
        $houseExistValidator = new HouseExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextErrorMock($houseExistConstraint->message, [
            '{{ city }}'    => 'Екатеринбург',
            '{{ street }}'  => 'Ленина',
            '{{ house }}'   => '1',
            '{{ company }}' => 'Управляющая компания 1'
        ]);
        $houseExistValidator->initialize($context);

        $houseExistValidator->validate(1, $houseExistConstraint);
    }
}
