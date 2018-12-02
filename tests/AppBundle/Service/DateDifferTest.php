<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\DateDiffer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DateDifferTest extends WebTestCase
{
    /**
     * @return array
     */
    public function providerGetDays()
    {
        return [
            'дата1 больше чем дата2 на 1 день'  =>  [
                new \DateTime('2018-01-10'),//дата1
                new \DateTime('2018-01-09'),//дата2
                -1//ожидаемый результат
            ],
            'дата1 больше чем дата2 на 10 дней' =>  [
                new \DateTime('2018-01-11'),
                new \DateTime('2018-01-01'),
                -10
            ],
            'дата1 меньше чем дата2 на 1 день'  =>  [
                new \DateTime('2018-01-01'),
                new \DateTime('2018-01-02'),
                1
            ],
            'дата1 меньше чем дата2 на 10 дней' =>  [
                new \DateTime('2018-01-01'),
                new \DateTime('2018-01-11'),
                10
            ]
        ];
    }

    /**
     * вычисление разницы дней между датами
     * @dataProvider providerGetDays
     * @param \DateTime $date1
     * @param \DateTime $date2
     * @param $expected
     */
    public function testGetDays(\DateTime $date1, \DateTime $date2, $expected)
    {
        $dateDifferService = new DateDiffer();
        $result = $dateDifferService->getDays($date1, $date2);

        $this->assertEquals($expected, $result);
    }
}
