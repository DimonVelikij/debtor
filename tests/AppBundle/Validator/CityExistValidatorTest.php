<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Entity\City;
use AppBundle\Validator\Constraints\CityExist;
use AppBundle\Validator\Constraints\CityExistValidator;

class CityExistValidatorTest extends ValidatorTestCase
{
    /**
     * добавление города Москва, города Москва в базе не существует
     */
    public function testAddCityValidate()
    {
        $cityExistConstraints = new CityExist();
        $cityExistValidator = new CityExistValidator($this->getEntityManager());
        $formData = (new City())
            ->setTitle('Москва');

        $context = $this->getExecutionContextOkWithDataMock($formData);
        $cityExistValidator->initialize($context);

        $cityExistValidator->validate('Москва', $cityExistConstraints);
    }

    /**
     * редактирование города, меняем Екатеринбург на Москва, города Москва в базе не существует
     */
    public function testRenameCityValidate()
    {
        $cityExistConstraints = new CityExist();
        $cityExistValidator = new CityExistValidator($this->getEntityManager());
        $formData = $this->getEntityManager()->getRepository('AppBundle:City')->findOneBy(['title' => 'Екатеринбург']);

        $context = $this->getExecutionContextOkWithDataMock($formData);
        $cityExistValidator->initialize($context);

        $cityExistValidator->validate('Москва', $cityExistConstraints);
    }

    /**
     * редактирование города Екатеринбург без переименования, город Екатеринбург в базе существует
     */
    public function testEditCityValidate()
    {
        $cityExistConstraints = new CityExist();
        $cityExistValidator = new CityExistValidator($this->getEntityManager());
        $formData = $this->getEntityManager()->getRepository('AppBundle:City')->findOneBy(['title' => 'Екатеринбург']);

        $context = $this->getExecutionContextOkWithDataMock($formData);
        $cityExistValidator->initialize($context);

        $cityExistValidator->validate('Екатеринбург', $cityExistConstraints);
    }

    /**
     * добавление города Екатеринбург, город Екатерибург в базе существует
     */
    public function testAddCityInvalidate()
    {
        $cityExistConstraints = new CityExist();
        $cityExistValidator = new CityExistValidator($this->getEntityManager());
        $formData = new City();

        $context = $this->getExecutionContextErrorWithDataMock($formData, $cityExistConstraints->message, ['{{ city }}', 'Екатеринбург']);
        $cityExistValidator->initialize($context);

        $cityExistValidator->validate('Екатеринбург', $cityExistConstraints);
    }
}
