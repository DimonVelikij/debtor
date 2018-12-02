<?php

namespace AppBundle\Service;

class DutyCalculator
{
    /**
     * вычисление пошлины при подаче искового заявления в суд общей юрисдикции
     * @param $value
     * @return string
     */
    public function calculationCGJStatementClaim($value)
    {
        if ($value <= 20000) {//до 20000
            return $value * 0.04 > 400 ?
                $this->prepareValue(400) :
                $this->prepareValue($value * 0.04);
        } elseif ($value > 20000 && $value <= 100000) {//от 20001 до 100000
            return $this->prepareValue(800 + ($value - 20000) * 0.03);
        } elseif ($value > 100000 && $value <= 200000) {//от 100001 до 200000
            return $this->prepareValue(3200 + ($value - 100000) * 0.02);
        } elseif ($value > 200000 && $value <= 1000000) {//от 200001 до 1000000
            return $this->prepareValue(5200 + ($value - 200000) * 0.01);
        } else {
            return 13200 + ($value - 1000000) * 0.005 > 60000 ?
                $this->prepareValue(60000) :
                $this->prepareValue(13200 + ($value - 1000000) * 0.005);
        }
    }

    /**
     * вычисление пошлины на выдачу судбного приказа в суд общей юрисдикции
     * @param $value
     * @return string
     */
    public function calculationCGJCourtOrder($value)
    {
        return $this->prepareValue((float)str_replace(' ', '', $this->calculationCGJStatementClaim($value)) * 0.5);
    }

    /**
     * вычисление пошлины при подаче искового заявления в арбитражный суд
     * @param $value
     * @return string
     */
    public function calculationACStatementClaim($value)
    {
        if ($value <= 100000) {//до 100000
            return $value * 0.04 < 2000 ?
                $this->prepareValue(2000) :
                $this->prepareValue($value * 0.04);
        } elseif ($value > 100000 && $value <= 200000) {//от 100001 до 200000
            return $this->prepareValue(4000 + ($value - 100000) * 0.03);
        } elseif ($value > 200000 && $value <= 1000000) {//от 200001 до 1000000
            return $this->prepareValue(7000 + ($value - 200000) * 0.02);
        } elseif ($value > 1000000 && $value <= 2000000) {//от 1000001 до 2000000
            return $this->prepareValue(23000 + ($value - 1000000) * 0.01);
        } else {
            return 33000 + ($value - 2000000) * 0.005 > 200000 ?
                $this->prepareValue(200000) :
                $this->prepareValue(33000 + ($value - 2000000) * 0.005);
        }
    }

    /**
     * вычисление пошлины на выдачу судбного приказа в арбитражный суд
     * @param $value
     * @return string
     */
    public function calculationACCourtOrder($value)
    {
        return $this->prepareValue((float)str_replace(' ', '', $this->calculationACStatementClaim($value)) * 0.5);
    }

    /**
     * форматирование получившегося числа
     * @param $value
     * @return string
     */
    private function prepareValue($value)
    {
        return number_format($value, 2, '.', '');
    }
}