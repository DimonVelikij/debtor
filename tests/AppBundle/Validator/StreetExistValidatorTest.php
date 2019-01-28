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
        $streetExistConstraint = new StreetExist();
        $streetExistValidator = new StreetExistValidator($this->getEntityManager());
        $formData = (new Street())
            ->setTitle('Малышева')
            ->setCity((new City())
                ->setTitle('Екатеринбург')
            );
        $streetExistValidator->initialize($this->configureContextValidator($formData));

        $streetExistValidator->validate('Малышева', $streetExistConstraint);
    }

    /**
     * редактирование улицы, меняем Ленина на Малышева, улицы Малышева в баще не существует
     */
    public function testRenameStreetValidate()
    {
        $streetExistConstraint = new StreetExist();
        $streetExistValidator = new StreetExistValidator($this->getEntityManager());
        $formData = $this->getEntityManager()->getRepository('AppBundle:Street')->findOneBy(['title' => 'Ленина']);
        $streetExistValidator->initialize($this->configureContextValidator($formData));

        $streetExistValidator->validate('Малышева', $streetExistConstraint);
    }

    /**
     * редактирование улицы Ленина без переименования, улица Ленина в базе существует
     */
    public function testEditValidate()
    {
        $streetExistConstraint = new StreetExist();
        $streetExistValidator = new StreetExistValidator($this->getEntityManager());
        $formData = $this->getEntityManager()->getRepository('AppBundle:Street')->findOneBy(['title' => 'Ленина']);
        $streetExistValidator->initialize($this->configureContextValidator($formData));

        $streetExistValidator->validate('Ленина', $streetExistConstraint);
    }

    /**
     * добавление улицы Ленина, улица Ленина существует в базе
     */
    public function testAddInvalidate()
    {
        $streetExistConstraint = new StreetExist();
        $streetExistValidator = new StreetExistValidator($this->getEntityManager());
        $formData = (new Street())
            ->setTitle('Ленина')
            ->setCity((new City())
                ->setTitle('Екатеринбург')
            );
        $streetExistValidator->initialize($this->configureContextValidator($formData, $streetExistConstraint->message, [
            '{{ city }}' => 'Екатеринбург',
            '{{ street }}' => 'Ленина'
        ]));

        $streetExistValidator->validate('Ленина', $streetExistConstraint);
    }
}
