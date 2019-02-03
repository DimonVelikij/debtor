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
     * добаление дома №200 на улицу Ленина города Екатеринбурга, дома №2 в базе не существует
     */
    public function testAddHouseValidate()
    {
        $houseExistConstraint = new HouseExist();
        $houseExistValidator = new HouseExistValidator($this->getEntityManager());
        $formData = (new House())
            ->setNumber(200)
            ->setStreet((new Street())
                ->setTitle('Ленина')
                ->setCity((new City())
                    ->setTitle('Екатеринбург')
                )
            );

        $context = $this->getExecutionContextOkWithDataMock($formData);
        $houseExistValidator->initialize($context);

        $houseExistValidator->validate(2, $houseExistConstraint);
    }

    /**
     * редактирование дома, меняем №1 на №200, №200 в базе не существует
     */
    public function testRenameHouseValidate()
    {
        $houseExistConstraint = new HouseExist();
        $houseExistValidator = new HouseExistValidator($this->getEntityManager());
        $formData = $this->getEntityManager()->getRepository('AppBundle:House')->findOneBy(['number' => 1]);

        $context = $this->getExecutionContextOkWithDataMock($formData);
        $houseExistValidator->initialize($context);

        $houseExistValidator->validate(200, $houseExistConstraint);
    }

    /**
     * редактирование №1 без переименования, №1 в базе существует
     */
    public function testEditValidate()
    {
        $houseExistConstraint = new HouseExist();
        $houseExistValidator = new HouseExistValidator($this->getEntityManager());
        $formData = $this->getEntityManager()->getRepository('AppBundle:House')->findOneBy(['number' => 1]);

        $context = $this->getExecutionContextOkWithDataMock($formData);
        $houseExistValidator->initialize($context);

        $houseExistValidator->validate(1, $houseExistConstraint);
    }

    /**
     * добавление №1 на улицу Ленина города Екатеринбург, дом №1 в базе уже существует
     */
    public function testAddInvalidate()
    {
        $houseExistConstraint = new HouseExist();
        $houseExistValidator = new HouseExistValidator($this->getEntityManager());
        $formData = (new House())
            ->setNumber(1)
            ->setStreet((new Street())
                ->setTitle('Ленина')
                ->setCity((new City())
                    ->setTitle('Екатеринбург')
                )
            );

        $context = $this->getExecutionContextErrorWithDataMock($formData, $houseExistConstraint->message, [
            '{{ city }}' => 'Екатеринбург',
            '{{ street }}' => 'Ленина',
            '{{ house }}' => '1',
            '{{ company }}' => 'Управляющая компания 1'
        ]);
        $houseExistValidator->initialize($context);

        $houseExistValidator->validate(1, $houseExistConstraint);
    }
}
