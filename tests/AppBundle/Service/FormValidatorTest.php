<?php

namespace Tests\AppBundle\Service;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class FormValidatorTest extends ServiceTestCase
{
    /**
     * @return array
     */
    public function dataValidate()
    {
        return [
            'Валидация прошла'  =>  [
                [
                    'title' =>  'Название',
                    'date'  =>  '01.01.2000',
                    'email' =>  'test@mail.ru'
                ],
                [
                    'title' =>  [
                        new NotBlank(['message' => 'Укажите название'])
                    ],
                    'date'  =>  [
                        new Regex(['pattern' => '/^([0-2]\d|3[01])\.(0\d|1[012])\.(19|20)(\d\d)$/', 'message' => 'Неверно указана дата'])
                    ],
                    'email' =>  [
                        new Email(['message' => 'Неверно введен E-mail'])
                    ]
                ],
                []
            ],
            'Валидация не прошла'   =>  [
                [
                    'title' =>  null,
                    'date'  =>  'date',
                    'email' =>  'email'
                ],
                [
                    'title' =>  [
                        new NotBlank(['message' => 'Укажите название'])
                    ],
                    'date'  =>  [
                        new Regex(['pattern' => '/^([0-2]\d|3[01])\.(0\d|1[012])\.(19|20)(\d\d)$/', 'message' => 'Неверно указана дата'])
                    ],
                    'email' =>  [
                        new Email(['message' => 'Неверно введен E-mail'])
                    ]
                ],
                [
                    'title' =>  'Укажите название',
                    'date'  =>  'Неверно указана дата',
                    'email' =>  'Неверно введен E-mail'
                ]
            ]
        ];
    }

    /**
     * @dataProvider dataValidate
     * @param $input
     * @param $constraints
     * @param $result
     */
    public function testValidate($input, $constraints, $result)
    {
        $formValidator = $this->getContainer()->get('app.service.form_validator');

        $errors = $formValidator->validate($input, $constraints);

        $this->assertEquals($result, $errors);
    }
}
