<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="flats")
 *
 * Class Flat
 * @package AppBundle\Entity
 */
class Flat
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="number", type="string", length=255, nullable=true)
     *
     * номер помещения (квартиры)
     */
    private $number;

    /**
     * @ORM\Column(name="start_debt_period", type="date", nullable=true)
     *
     * начало периода взыскания
     */
    private $startDebtPeriod;

    /**
     * @ORM\Column(name="end_debt_period", type="date", nullable=true)
     *
     * конец периода взыскания
     */
    private $endDebtPeriod;

    /**
     * @ORM\Column(name="date_fill_debt", type="date", nullable=true)
     *
     * дата заполнения долга
     */
    private $dateFillDebt;

    /**
     * @ORM\Column(name="sum_debt", type="float", precision=10, scale=2, nullable=false)
     *
     * сумма долга
     */
    private $sumDebt;

    /**
     * @ORM\Column(name="period_accrued_debt", type="float", precision=10, scale=2, nullable=true)
     *
     * за период начислено долга
     */
    private $periodAccruedDebt;

    /**
     * @ORM\Column(name="period_pay_debt", type="float", precision=10, scale=2, nullable=true)
     *
     * за период оплачено долга
     */
    private $periodPayDebt;

    /**
     * @ORM\Column(name="date_fill_fine", type="date", nullable=true)
     *
     * дата заполнения пени
     */
    private $dateFillFine;

    /**
     * @ORM\Column(name="sum_fine", type="float", precision=10, scale=2, nullable=true)
     *
     * сумма пени
     */
    private $sumFine;

    /**
     * @ORM\Column(name="period_accrued_fine", type="float", precision=10, scale=2, nullable=true)
     *
     * за период начислено пени
     */
    private $periodAccruedFine;

    /**
     * @ORM\Column(name="period_pay_fine", type="float", precision=10, scale=2, nullable=true)
     *
     * за период оплачено пени
     */
    private $periodPayFine;

    /**
     * @ORM\Column(name="arhive", type="boolean", nullable=true)
     *
     * перестал быть должником
     */
    private $archive;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     *
     * дата последнего обновления
     */
    private $updatedAt;

    /**
     * @ORM\Column(name="personal_accounts", type="object", nullable=false)
     *
     * лицевые счета
     */
    private $personalAccounts;

    /**
     * @ORM\ManyToOne(targetEntity="House", inversedBy="flats")
     * @ORM\JoinColumn(name="house_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $house;

    /**
     * @ORM\OneToMany(targetEntity="Debtor", mappedBy="flat")
     */
    private $debtors;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ?
            $this->getHouse()->getStreet()->getCity()->getTitle() . ', ' .
            $this->getHouse()->getStreet()->getTitle() . ', ' .
            $this->getHouse()->getNumber() . ($this->number ?: '') :
            '';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->debtors = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set number
     *
     * @param string $number
     *
     * @return Flat
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set startDebtPeriod
     *
     * @param \DateTime $startDebtPeriod
     *
     * @return Flat
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
     * @return Flat
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
     * @return Flat
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
     * @return Flat
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
     * @return Flat
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
     * @return Flat
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
     * @return Flat
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
     * @return Flat
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
     * @return Flat
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
     * @return Flat
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
     * Set archive
     *
     * @param boolean $archive
     *
     * @return Flat
     */
    public function setArchive($archive)
    {
        $this->archive = $archive;

        return $this;
    }

    /**
     * Get archive
     *
     * @return boolean
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Flat
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set personalAccounts
     *
     * @param \stdClass $personalAccounts
     *
     * @return Flat
     */
    public function setPersonalAccounts($personalAccounts)
    {
        $this->personalAccounts = $personalAccounts;

        return $this;
    }

    /**
     * Get personalAccounts
     *
     * @return \stdClass
     */
    public function getPersonalAccounts()
    {
        return $this->personalAccounts;
    }

    /**
     * Set house
     *
     * @param \AppBundle\Entity\House $house
     *
     * @return Flat
     */
    public function setHouse(\AppBundle\Entity\House $house)
    {
        $this->house = $house;

        return $this;
    }

    /**
     * Get house
     *
     * @return \AppBundle\Entity\House
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * Add debtor
     *
     * @param \AppBundle\Entity\Debtor $debtor
     *
     * @return Flat
     */
    public function addDebtor(\AppBundle\Entity\Debtor $debtor)
    {
        $this->debtors[] = $debtor;

        return $this;
    }

    /**
     * Remove debtor
     *
     * @param \AppBundle\Entity\Debtor $debtor
     */
    public function removeDebtor(\AppBundle\Entity\Debtor $debtor)
    {
        $this->debtors->removeElement($debtor);
    }

    /**
     * Get debtors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDebtors()
    {
        return $this->debtors;
    }
}
