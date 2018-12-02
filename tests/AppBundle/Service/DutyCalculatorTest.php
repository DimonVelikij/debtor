<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DutyCalculatorTest extends WebTestCase
{
    /**
     * @return array
     */
    public function providerCalculationCGJ()
    {
        return [
            'сумма долга менее 20000, чтобы пошлина получилась менее 400'   =>  [
                6000,//сумма долга
                '240.00',//пошлина при подаче искового заявления в суд общей юрисдикции
                '120.00'//пошлина на выдачу судбного приказа в суд общей юрисдикции
            ],
            'сумма долга менее 20000, чтобы пошлина получилась более 400'   =>  [
                16000,
                '400.00',
                '200.00'
            ],
            'сумма долга от 20001 до 100000, вариант1'                      =>  [
                35000,
                '1250.00',
                '625.00'
            ],
            'сумма долга от 20001 до 100000, вариант2'                      =>  [
                74390,
                '2431.70',
                '1215.85'
            ],
            'сумма долга от 100001 до 200000, вариант1'                     =>  [
                135000,
                '3900.00',
                '1950.00'
            ],
            'сумма долга от 100001 до 200000, вариант2'                     =>  [
                189304,
                '4986.08',
                '2493.04'
            ],
            'сумма долга от 200001 до 1000000, вариант1'                    =>  [
                356879,
                '6768.79',
                '3384.40'
            ],
            'сумма долга от 200001 до 1000000, вариант2'                    =>  [
                767856,
                '10878.56',
                '5439.28'
            ],
            'сумма долга более 1000000, чтобы пошлина получилась менее 60000'   =>  [
                1200000,
                '14200.00',
                '7100.00'
            ],
            'сумма долга более 100000, чтобы пошлина получилась более 60000'    =>  [
                15000000,
                '60000.00',
                '30000.00'
            ]
        ];
    }

    /**
     * вычисление пошлины при подаче искового заявления и на выдачу судбного приказа в суд общей юрисдикции
     * @dataProvider providerCalculationCGJ
     * @param $value
     * @param $expectedCGJStatementClaim
     * @param $expectedCGJCourtOrder
     */
    public function testCalculationCGJ($value, $expectedCGJStatementClaim, $expectedCGJCourtOrder)
    {
        $dutyCalculator = new DutyCalculator();

        $this->assertEquals($expectedCGJStatementClaim, $dutyCalculator->calculationCGJStatementClaim($value));
        $this->assertEquals($expectedCGJCourtOrder, $dutyCalculator->calculationCGJCourtOrder($value));
    }

    /**
     * @return array
     */
    public function providerCalculationAC()
    {
        return [
            'сумма долга менее 100000, чтобы пошлина получилась менее 2000' =>  [
                50000,//сумма долга
                '2000',//пошлина при подаче искового заявления в арбитражный суд
                '1000'//пошлина на выдачу судбного приказа в арбитражный суд
            ],
            'сумма долга менее 100000, чтобы пошлина получилась более 2000' =>  [
                92000,
                '3680.00',
                '1840.00'
            ],
            'сумма долга от 100001 до 200000, вариант 1'                        =>  [
                120000,
                '4600.00',
                '2300.00'
            ],
            'сумма долга от 100001 до 200000, вариант 2'                        =>  [
                187300,
                '6619.00',
                '3309.50'
            ],
            'сумма долга от 200001 до 1000000, вариант 1'                       =>  [
                311050,
                '9221.00',
                '4610.50'
            ],
            'сумма долга от 200001 до 1000000, вариант 2'                       =>  [
                845679,
                '19913.58',
                '9956.79'
            ],
            'сумма долга от 1000001 до 2000000, вариант 1'                      =>  [
                1010101,
                '23101.01',
                '11550.51'
            ],
            'сумма долга от 1000001 до 2000000, вариант 2'                      =>  [
                1789987,
                '30899.87',
                '15449.94'
            ],
            'сумма долга более 2000000, но пошлина менее 200000'                =>  [
                3000000,
                '38000.00',
                '19000.00'
            ],
            'сумма долга более 2000000, но пошлина более 200000'                =>  [
                36000000,
                '200000',
                '100000'
            ]
        ];
    }

    /**
     * вычисление пошлины при подаче искового заявления и на выдачу судбного приказа в арбитражный суд
     * @dataProvider providerCalculationAC
     * @param $value
     * @param $expectedACStatementClaim
     * @param $expectedACCourtOrder
     */
    public function testCalculationAC($value, $expectedACStatementClaim, $expectedACCourtOrder)
    {
        $dutyCalculator = new DutyCalculator();

        $this->assertEquals($expectedACStatementClaim, $dutyCalculator->calculationACStatementClaim($value));
        $this->assertEquals($expectedACCourtOrder, $dutyCalculator->calculationACCourtOrder($value));
    }
}
