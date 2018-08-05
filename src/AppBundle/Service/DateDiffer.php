<?php

namespace AppBundle\Service;

class DateDiffer
{
    /**
     * вычисление разницы дней между датами
     * если $date1 = 10.01.2000, а $date2 = 08.01.2000 - функция вернет -2
     * @param \DateTime $date1
     * @param \DateTime $date2
     * @return int
     */
    public function getDays(\DateTime $date1, \DateTime $date2)
    {
        $dateDiff = $date1->diff($date2);

        return $dateDiff->days * ($dateDiff->invert ? -1 : 1);
    }
}