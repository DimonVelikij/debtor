<?php

namespace AppBundle\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class FormValidator
{
    /** @var ValidatorInterface  */
    private $validator;

    /**
     * FormValidator constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * валидация форм
     * @param array $input
     * @param array $constraints
     * @return array
     */
    public function validate(array $input, array $constraints)
    {
        $errors = [];

        foreach ($constraints as $field => $constraint) {
            $validationResult = $this->validator->validate($input[$field], $constraint);

            if (count($validationResult)) {
                $errors[$field] = $validationResult[0]->getMessage();
            }
        }

        return $errors;
    }
}
