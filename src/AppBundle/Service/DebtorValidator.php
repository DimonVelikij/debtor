<?php

namespace AppBundle\Service;

use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\TraceableValidator;

class DebtorValidator
{
    /** @var TraceableValidator  */
    private $validator;

    /** @var DataCollectorTranslator  */
    private $translator;

    /**
     * DebtorValidator constructor.
     * @param TraceableValidator $validator
     * @param DataCollectorTranslator $translator
     */
    public function __construct(
        TraceableValidator $validator,
        DataCollectorTranslator $translator)
    {
        $this->validator = $validator;
        $this->translator = $translator;
    }

    /**
     * подготовка данных к проверке
     * @param array $debtorData
     * @return array
     */
    public function prepareData(array $debtorData)
    {
        return [
            'debtorType'        =>  $debtorData['debtorType'] ?? null,
            'company'           =>  $debtorData['company'] ?? null,
            'name'              =>  $debtorData['name'] ?? null,
            'phone'             =>  $debtorData['phone'] ?? null,
            'email'             =>  $debtorData['email'] ?? null,
            'location'          =>  $debtorData['location'] ?? null,
            'dateOfBirth'       =>  $debtorData['dateOfBirth'] ?? null,
            'placeOfBirth'      =>  $debtorData['placeOfBirth'] ?? null,
            'ogrnip'            =>  $debtorData['ogrnip'] ?? null,
            'inn'               =>  $debtorData['inn'] ?? null,
            'ogrn'              =>  $debtorData['ogrn'] ?? null,
            'bossName'          =>  $debtorData['bossName'] ?? null,
            'bossPosition'      =>  $debtorData['bossPosition'] ?? null,
            'startDebtPeriod'   =>  $debtorData['startDebtPeriod'] ?? null,
            'endDebtPeriod'     =>  $debtorData['endDebtPeriod'] ?? null,
            'dateFillDebt'      =>  $debtorData['dateFillDebt'] ?? null,
            'sumDebt'           =>  $debtorData['sumDebt'] ?? null,
            'periodAccruedDebt' =>  $debtorData['periodAccruedDebt'] ?? null,
            'periodPayDebt'     =>  $debtorData['periodPayDebt'] ?? null,
            'dateFillFine'      =>  $debtorData['dateFillFine'] ?? null,
            'sumFine'           =>  $debtorData['sumFine'] ?? null,
            'periodAccruedFine' =>  $debtorData['periodAccruedFine'] ?? null,
            'periodPayFine'     =>  $debtorData['periodPayFine'] ?? null
        ];
    }

    /**
     * валидация данных
     * @param array $debtorData
     * @return array
     * @throws \Exception
     */
    public function validate(array $debtorData)
    {
        if (!$debtorData['debtorType']) {
            return [
                'success'   =>  false,
                'errors'    =>  ['debtorType' => 'Выберите тип должника']
            ];
        }

        $constraintsMethod = $this->getConstraintsMethod($debtorData['debtorType']);

        if (!method_exists($this, $constraintsMethod)) {
            throw new \Exception("Undefined method '{$constraintsMethod}'");
        }

        $errors = [];

        $constraints = $this->$constraintsMethod();

        foreach ($constraints as $fieldName => $constraint) {
            $validationResult = $this->validator->validate($debtorData[$fieldName], $constraint);

            if (count($validationResult)) {
                $errors[$fieldName] = $this->translator->trans($validationResult[0]->getMessage(), [], 'trforms');
            }
        }

        if (count($errors)) {
            return [
                'success'   =>  false,
                'errors'    =>  $errors
            ];
        }

        return [
            'success'   =>  true
        ];
    }

    /**
     * метод вализации в зависимости от типа должника
     * @param array $debtorType
     * @return string
     */
    private function getConstraintsMethod(array $debtorType)
    {
        return 'get' . str_replace('_', '', ucwords($debtorType['alias'], '_')) . 'Constraints';
    }

    /**
     * ограничения для всех типов должников
     * @return array
     */
    private function getBaseConstraints()
    {
        return [
            'startDebtPeriod'   =>  [
                new Regex(['pattern' => '/^([0-2]\d|3[01])(0\d|1[012])(19|20)(\d\d)$/', 'message' => 'Неверно указана дата начала периода взыскания'])
            ],
            'endDebtPeriod'     =>  [
                new Regex(['pattern' => '/^([0-2]\d|3[01])(0\d|1[012])(19|20)(\d\d)$/', 'message' => 'Неверно указана дата конца периода взыскания'])
            ],
            'dateFillDebt'      =>  [
                new Regex(['pattern' => '/^([0-2]\d|3[01])(0\d|1[012])(19|20)(\d\d)$/', 'message' => 'Неверно указана дата заполнения'])
            ],
            'sumDebt'           =>  [
                new Regex(['pattern' => '/^\d+$/', 'message' => 'Неверно указана сумма долга'])
            ],
            'periodAccruedDebt' =>  [
                new NotBlank(['message' => 'Укажите сумму начисления']),
                new Regex(['pattern'    => '/^\d+$/', 'message' => 'Неверно указана сумма начисления'])
            ],
            'periodPayDebt'     =>  [
                new NotBlank(['message' => 'Укажите сумму оплаты']),
                new Regex(['pattern'    => '/^\d+$/', 'message' => 'Неверно указана сумма оплаты'])
            ],
            'dateFillFine'      =>  [
                new Regex(['pattern'    =>  '/^([0-2]\d|3[01])(0\d|1[012])(19|20)(\d\d)$/', 'message' => 'Неверно указана дата заполнения'])
            ],
            'sumFine'           =>  [
                new Regex(['pattern'    =>  '/^\d+$/', 'message' => 'Неверно указана сумма долга'])
            ],
            'periodAccruedFine' =>  [
                new NotBlank(['message' =>  'Укажите сумму начисления']),
                new Regex(['pattern'    =>  '/^\d+$/', 'message' => 'Неверно указана сумма начисления'])
            ],
            'periodPayFine'     =>  [
                new NotBlank(['message' =>  'Укажите сумму оплаты']),
                new Regex(['pattern'    =>  '/^\d+$/', 'message' => 'Неверно указана сумма оплаты'])
            ],
            'phone'             =>  [
                new NotBlank(['message' =>  'Укажите телефон']),
                new Regex(['pattern'    =>  '/^\d+$/', 'message' => 'Невено указан телефон'])
            ],
            'email'             =>  [
                new NotBlank(['message' =>  'Укажите E-mail']),
                new Email(['message'    =>  'Неверно введен E-mail'])
            ],
            'company'           =>  [
                new NotBlank(['message' =>  'Выберите управлющую компанию'])
            ]
        ];
    }

    /**
     * ограничения для физических лиц
     * @return array
     */
    private function getIndividualConstraints()
    {
        $baseConstraints = $this->getBaseConstraints();
        $individualConstraints = [
            'name'              =>  [
                new NotBlank(['message' =>  'Укажите ФИО'])
            ],
            'dateOfBirth'       =>  [
                new Regex(['pattern'    =>  '/^([0-2]\d|3[01])(0\d|1[012])(19|20)(\d\d)$/', 'message' => 'Неверно указана дата рождения'])
            ],
            'location'          =>  [
                new NotBlank(['message' =>  'Укажите адрес местонаждения'])
            ]
        ];

        return array_merge($baseConstraints, $individualConstraints);
    }

    /**
     * ограничения для бизнесменов
     * @return array
     */
    private function getBusinessmanConstraints()
    {
        $baseConstraints = $this->getBaseConstraints();
        $businessmanConstraints = [

        ];

        return array_merge($baseConstraints, $businessmanConstraints);
    }

    /**
     * ограничения для юридических лиц
     * @return array
     */
    private function getLegalEntityConstraints()
    {
        $baseConstraints = $this->getBaseConstraints();
        $legalEntityConstraints = [
            'bossName'  =>  [
                new NotBlank(['message' => 'Укажите ФИО руководителя'])
            ],
            'bossPosition'  =>  [
                new NotBlank(['message' =>  'Укажите должность руководителя'])
            ]
        ];

        return array_merge($baseConstraints, $legalEntityConstraints);
    }
}