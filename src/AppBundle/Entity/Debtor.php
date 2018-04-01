<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="debtors")
 *
 * Class Debtor
 * @package AppBundle\Entity
 */
class Debtor
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(name="phone", type="string", length=255, nullable=false)
     */
    private $phone;

    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * начало периода взыскания
     * @ORM\Column(name="start_debt_period", type="date", nullable=true)
     */
    private $startDebtPeriod;

    /**
     * конец периода взыскания
     * @ORM\Column(name="end_debt_period", type="date", nullable=true)
     */
    private $endDebtPeriod;

    /**
     * дата заполнения долга
     * @ORM\Column(name="date_fill_debt", type="date", nullable=true)
     */
    private $dateFillDebt;

    /**
     * сумма долга
     * @ORM\Column(name="sum_debt", type="float", precision=10, scale=2, nullable=true)
     */
    private $sumDebt;

    /**
     * за период начислено долга
     * @ORM\Column(name="period_accrued_debt", type="float", precision=10, scale=2, nullable=false)
     */
    private $periodAccruedDebt;

    /**
     * за период оплачено долга
     * @ORM\Column(name="period_pay_debt", type="float", precision=10, scale=2, nullable=false)
     */
    private $periodPayDebt;

    /**
     * дата заполнения пени
     * @ORM\Column(name="date_fill_fine", type="date", nullable=true)
     */
    private $dateFillFine;

    /**
     * сумма пени
     * @ORM\Column(name="sum_fine", type="float", precision=10, scale=2, nullable=true)
     */
    private $sumFine;

    /**
     * за период начислено пени
     * @ORM\Column(name="period_accrued_fine", type="float", precision=10, scale=2, nullable=false)
     */
    private $periodAccruedFine;

    /**
     * за период оплачено пени
     * @ORM\Column(name="period_pay_fine", type="float", precision=10, scale=2, nullable=false)
     */
    private $periodPayFine;

    /**
     * @ORM\Column(name="arhive", type="boolean", nullable=true)
     */
    private $arhive;

    /**
     * @ORM\ManyToOne(targetEntity="DebtorStatus")
     * @ORM\JoinColumn(name="debtor_status_id", referencedColumnName="id", nullable=false)
     */
    private $debtorStatus;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="debtors")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     */
    private $company;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Debtor
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Debtor
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Debtor
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set startDebtPeriod
     *
     * @param \DateTime $startDebtPeriod
     *
     * @return Debtor
     */
    public function setStartDebtPeriod($startDebtPeriod)
    {
        $this->startDebtPeriod = $startDebtPeriod;

        return $this;
    }

    /**
     * Get startDebtPeriod
     *
     * @return \DateTime
     */
    public function getStartDebtPeriod()
    {
        return $this->startDebtPeriod;
    }

    /**
     * Set endDebtPeriod
     *
     * @param \DateTime $endDebtPeriod
     *
     * @return Debtor
     */
    public function setEndDebtPeriod($endDebtPeriod)
    {
        $this->endDebtPeriod = $endDebtPeriod;

        return $this;
    }

    /**
     * Get endDebtPeriod
     *
     * @return \DateTime
     */
    public function getEndDebtPeriod()
    {
        return $this->endDebtPeriod;
    }

    /**
     * Set dateFillDebt
     *
     * @param \DateTime $dateFillDebt
     *
     * @return Debtor
     */
    public function setDateFillDebt($dateFillDebt)
    {
        $this->dateFillDebt = $dateFillDebt;

        return $this;
    }

    /**
     * Get dateFillDebt
     *
     * @return \DateTime
     */
    public function getDateFillDebt()
    {
        return $this->dateFillDebt;
    }

    /**
     * Set sumDebt
     *
     * @param float $sumDebt
     *
     * @return Debtor
     */
    public function setSumDebt($sumDebt)
    {
        $this->sumDebt = $sumDebt;

        return $this;
    }

    /**
     * Get sumDebt
     *
     * @return float
     */
    public function getSumDebt()
    {
        return $this->sumDebt;
    }

    /**
     * Set periodAccruedDebt
     *
     * @param float $periodAccruedDebt
     *
     * @return Debtor
     */
    public function setPeriodAccruedDebt($periodAccruedDebt)
    {
        $this->periodAccruedDebt = $periodAccruedDebt;

        return $this;
    }

    /**
     * Get periodAccruedDebt
     *
     * @return float
     */
    public function getPeriodAccruedDebt()
    {
        return $this->periodAccruedDebt;
    }

    /**
     * Set periodPayDebt
     *
     * @param float $periodPayDebt
     *
     * @return Debtor
     */
    public function setPeriodPayDebt($periodPayDebt)
    {
        $this->periodPayDebt = $periodPayDebt;

        return $this;
    }

    /**
     * Get periodPayDebt
     *
     * @return float
     */
    public function getPeriodPayDebt()
    {
        return $this->periodPayDebt;
    }

    /**
     * Set dateFillFine
     *
     * @param \DateTime $dateFillFine
     *
     * @return Debtor
     */
    public function setDateFillFine($dateFillFine)
    {
        $this->dateFillFine = $dateFillFine;

        return $this;
    }

    /**
     * Get dateFillFine
     *
     * @return \DateTime
     */
    public function getDateFillFine()
    {
        return $this->dateFillFine;
    }

    /**
     * Set sumFine
     *
     * @param float $sumFine
     *
     * @return Debtor
     */
    public function setSumFine($sumFine)
    {
        $this->sumFine = $sumFine;

        return $this;
    }

    /**
     * Get sumFine
     *
     * @return float
     */
    public function getSumFine()
    {
        return $this->sumFine;
    }

    /**
     * Set periodAccruedFine
     *
     * @param float $periodAccruedFine
     *
     * @return Debtor
     */
    public function setPeriodAccruedFine($periodAccruedFine)
    {
        $this->periodAccruedFine = $periodAccruedFine;

        return $this;
    }

    /**
     * Get periodAccruedFine
     *
     * @return float
     */
    public function getPeriodAccruedFine()
    {
        return $this->periodAccruedFine;
    }

    /**
     * Set periodPayFine
     *
     * @param float $periodPayFine
     *
     * @return Debtor
     */
    public function setPeriodPayFine($periodPayFine)
    {
        $this->periodPayFine = $periodPayFine;

        return $this;
    }

    /**
     * Get periodPayFine
     *
     * @return float
     */
    public function getPeriodPayFine()
    {
        return $this->periodPayFine;
    }

    /**
     * Set arhive
     *
     * @param boolean $arhive
     *
     * @return Debtor
     */
    public function setArhive($arhive)
    {
        $this->arhive = $arhive;

        return $this;
    }

    /**
     * Get arhive
     *
     * @return boolean
     */
    public function getArhive()
    {
        return $this->arhive;
    }

    /**
     * Set debtorStatus
     *
     * @param \AppBundle\Entity\DebtorStatus $debtorStatus
     *
     * @return Debtor
     */
    public function setDebtorStatus(\AppBundle\Entity\DebtorStatus $debtorStatus)
    {
        $this->debtorStatus = $debtorStatus;

        return $this;
    }

    /**
     * Get debtorStatus
     *
     * @return \AppBundle\Entity\DebtorStatus
     */
    public function getDebtorStatus()
    {
        return $this->debtorStatus;
    }

    /**
     * Set company
     *
     * @param \AppBundle\Entity\Company $company
     *
     * @return Debtor
     */
    public function setCompany(\AppBundle\Entity\Company $company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \AppBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }
}
