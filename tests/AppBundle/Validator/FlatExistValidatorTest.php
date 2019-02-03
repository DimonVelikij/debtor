<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Entity\City;
use AppBundle\Entity\Flat;
use AppBundle\Entity\House;
use AppBundle\Entity\Street;
use AppBundle\Validator\Constraints\FlatExist;
use AppBundle\Validator\Constraints\FlatExistValidator;

class FlatExistValidatorTest extends ValidatorTestCase
{
    /**
     * добавление помещения №200 у дома №1 улицы Ленина города Екатеринбург, помещения №200 в базе не существует
     */
    public function testAddFlatValidate()
    {
        $flatExistConstraint = new FlatExist();
        $flatExistValidator = new FlatExistValidator($this->getEntityManager());
        $formData = (new Flat())
            ->setNumber(200)
            ->setHouse((new House())
                ->setNumber(1)
                ->setStreet((new Street())
                    ->setTitle('Ленина')
                    ->setCity((new City())
                        ->setTitle('Екатеринбург')
                    )
                )
            );

        $context = $this->getExecutionContextOkWithDataMock($formData);
        $flatExistValidator->initialize($context);

        $flatExistValidator->validate(200, $flatExistConstraint);
    }

    /**
     * редактирование помещения, меняем №1 на №200, №200 в бае не существует
     */
    public function testRenameFlatValidate()
    {
        $flatExistConstraint = new FlatExist();
        $flatExistValidator = new FlatExistValidator($this->getEntityManager());
        $formData = $this->getEntityManager()->getRepository('AppBundle:Flat')->findOneBy(['number' => 1]);

        $context = $this->getExecutionContextOkWithDataMock($formData);
        $flatExistValidator->initialize($context);

        $flatExistValidator->validate(200, $flatExistConstraint);
    }

    /**
     * редактирование №1 без переименования, №1 в базе существует
     */
    public function testEditFlatValidate()
    {
        $flatExistConstraint = new FlatExist();
        $flatExistValidator = new FlatExistValidator($this->getEntityManager());
        $formData = $this->getEntityManager()->getRepository('AppBundle:Flat')->findOneBy(['number' => 1]);

        $context = $this->getExecutionContextOkWithDataMock($formData);
        $flatExistValidator->initialize($context);

        $flatExistValidator->validate(1, $flatExistConstraint);
    }

    /**
     * добавление помещения №1 у дома №1 улицы Ленина города Екатеринбург, помещения №200 в базе существует
     */
    public function testAddFlatInvalidate()
    {
        $flatExistConstraint = new FlatExist();
        $flatExistValidator = new FlatExistValidator($this->getEntityManager());
        $formData = (new Flat())
            ->setNumber(1)
            ->setHouse((new House())
                ->setNumber(1)
                ->setStreet((new Street())
                    ->setTitle('Ленина')
                    ->setCity((new City())
                        ->setTitle('Екатеринбург')
                    )
                )
            );

        $context = $this->getExecutionContextErrorWithDataMock($formData, $flatExistConstraint->message, [
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
