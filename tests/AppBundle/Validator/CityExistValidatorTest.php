<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\CityExist;
use AppBundle\Validator\Constraints\CityExistValidator;

class CityExistValidatorTest extends ValidatorTestCase
{
    /**
     * добавление города Москва, города Москва в базе не существует
     */
    public function testAddCityValidate()
    {
        $cityExistConstraints = new CityExist(['cityId' => null]);
        $cityExistValidator = new CityExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextOkMock();
        $cityExistValidator->initialize($context);

        $cityExistValidator->validate('Москва', $cityExistConstraints);
    }

    /**
     * редактирование города, меняем Екатеринбург на Москва, города Москва в базе не существует
     */
    public function testRenameCityValidate()
    {
        $city = $this->getEntityManager()->getRepository('AppBundle:City')->findOneBy(['title' => 'Екатеринбург']);
        $cityExistConstraints = new CityExist(['cityId' => $city->getId()]);
        $cityExistValidator = new CityExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextOkMock();
        $cityExistValidator->initialize($context);

        $cityExistValidator->validate('Москва', $cityExistConstraints);
    }

    /**
     * редактирование города Екатеринбург без переименования, город Екатеринбург в базе существует
     */
    public function testEditCityValidate()
    {
        $city = $this->getEntityManager()->getRepository('AppBundle:City')->findOneBy(['title' => 'Екатеринбург']);
        $cityExistConstraints = new CityExist(['cityId' => $city->getId()]);
        $cityExistValidator = new CityExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextOkMock();
        $cityExistValidator->initialize($context);

        $cityExistValidator->validate('Екатеринбург', $cityExistConstraints);
    }

    /**
     * добавление города Екатеринбург, город Екатерибург в базе существует
     */
    public function testAddCityInvalidate()
    {
        $cityExistConstraints = new CityExist(['cityId' => false]);
        $cityExistValidator = new CityExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextErrorMock($cityExistConstraints->message, ['{{ city }}', 'Екатеринбург']);
        $cityExistValidator->initialize($context);

        $cityExistValidator->validate('Екатеринбург', $cityExistConstraints);
    }
}
