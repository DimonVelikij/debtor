<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\FlatExist;
use AppBundle\Validator\Constraints\FlatExistValidator;

class FlatExistValidatorTest extends ValidatorTestCase
{
    /**
     * добавление помещения №200 у дома №1 улицы Ленина города Екатеринбург, помещения №200 в базе не существует
     */
    public function testAddFlatValidate()
    {
        $flatExistConstraint = new FlatExist(['flatId' => null]);
        $flatExistValidator = new FlatExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextOkMock();
        $flatExistValidator->initialize($context);

        $flatExistValidator->validate(200, $flatExistConstraint);
    }

    /**
     * редактирование помещения, меняем №1 на №200, №200 в бае не существует
     */
    public function testRenameFlatValidate()
    {
        $flat = $this->getEntityManager()->getRepository('AppBundle:Flat')->findOneBy(['number' => '1']);
        $flatExistConstraint = new FlatExist(['flatId' => $flat->getId()]);
        $flatExistValidator = new FlatExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextOkMock();
        $flatExistValidator->initialize($context);

        $flatExistValidator->validate(200, $flatExistConstraint);
    }

    /**
     * редактирование №1 без переименования, №1 в базе существует
     */
    public function testEditFlatValidate()
    {
        $flat = $this->getEntityManager()->getRepository('AppBundle:Flat')->findOneBy(['number' => '1']);
        $flatExistConstraint = new FlatExist(['flatId' => $flat->getId()]);
        $flatExistValidator = new FlatExistValidator($this->getEntityManager());

        $context = $this->getExecutionContextOkMock();
        $flatExistValidator->initialize($context);

        $flatExistValidator->validate(1, $flatExistConstraint);
    }

    /**
     * добавление помещения №1 у дома №1 улицы Ленина города Екатеринбург, помещения №1 в базе существует
     */
    public function testAddFlatInvalidate()
    {
        $flatExistConstraint = new FlatExist(['flatId' => null]);
        $flatExistValidator = new FlatExistValidator($this->getEntityManager());
        $context = $this->getExecutionContextErrorMock($flatExistConstraint->message, [
            '{{ company }}' =>  'Управляющая компания 1',
            '{{ city }}'    =>  'Екатеринбург',
            '{{ street }}'  =>  'Ленина',
            '{{ house }}'   =>  '1',
            '{{ flat }}'    =>  '1'
        ]);
        $flatExistValidator->initialize($context);

        $flatExistValidator->validate(1, $flatExistConstraint);
    }
}
