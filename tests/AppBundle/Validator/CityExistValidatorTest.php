<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Entity\City;
use AppBundle\Validator\Constraints\CityExist;
use AppBundle\Validator\Constraints\CityExistValidator;

class CityExistValidatorTest extends ValidatorTestCase
{
    /**
     * добавлениего города Москва, города Москва в базе не существует
     */
    public function testAddCityValidate()
    {
        $cityExistConstraints = new CityExist();
        $cityExistValidator = new CityExistValidator($this->getEntityManager());
        $formData = new City();
        $cityExistValidator->initialize($this->configureContextValidator($formData));

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
        $cityExistValidator->initialize($this->configureContextValidator($formData));

        $cityExistValidator->validate('Москва', $cityExistConstraints);
    }

    /**
     * редактирование города Екатеринбург без переименования, город Екатеринбург в базе существует
     */
    public function testEditValidate()
    {
        $cityExistConstraints = new CityExist();
        $cityExistValidator = new CityExistValidator($this->getEntityManager());
        $formData = $this->getEntityManager()->getRepository('AppBundle:City')->findOneBy(['title' => 'Екатеринбург']);
        $cityExistValidator->initialize($this->configureContextValidator($formData));

        $cityExistValidator->validate('Екатеринбург', $cityExistConstraints);
    }

    /**
     * добавление города Екатеринбург, город Екатерибург в базе существует
     */
    public function testAddInvalidate()
    {
        $cityExistConstraints = new CityExist();
        $cityExistValidator = new CityExistValidator($this->getEntityManager());
        $formData = new City();
        $cityExistValidator->initialize($this->configureContextValidator($formData, $cityExistConstraints->message, ['{{ string }}', 'Екатеринбург']));

        $cityExistValidator->validate('Екатеринбург', $cityExistConstraints);
    }
}
