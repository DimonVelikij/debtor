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
     * ФИО или наименование организации
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * телефон
     * @ORM\Column(name="phone", type="string", length=255, nullable=false)
     */
    private $phone;

    /**
     * email
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * место нахождения или жительства
     * @ORM\Column(name="location", type="string", length=255, nullable=false)
     */
    private $location;

    /**
     * дата начала собственности
     * @ORM\Column(name="start_date_ownership", type="date", nullable=true)
     */
    private $startDateOwnership;

    /**
     * дата конча собственности
     * @ORM\Column(name="end_date_ownership", type="date", nullable=true)
     */
    private $endDateOwnership;

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
     * архив
     * @ORM\Column(name="arhive", type="boolean", nullable=true)
     */
    private $arhive;

    /**
     * дата рождения физ лица
     * @ORM\Column(name="date_of_birth", type="date", nullable=true)
     */
    private $dateOfBirth;

    /**
     * место рождения физ лица
     * @ORM\Column(name="place_of_birth", type="string", length=255, nullable=true)
     */
    private $placeOfBirth;

    /**
     * ФИО собственника, если статус - законный представитель несовершеннолетнего собственника
     * @ORM\Column(name="owner_name", type="string", length=255, nullable=true)
     */
    private $ownerName;

    /**
     * ОГРНИП индивидульного предпринимателя
     * @ORM\Column(name="ogrnip", type="string", length=255, nullable=true)
     */
    private $ogrnip;

    /**
     * ИНН индивидуального предпринимателя или Юр. лица
     * @ORM\Column(name="inn", type="string", length=255, nullable=true)
     */
    private $inn;

    /**
     * ОГРН Юр. лица
     * @ORM\Column(name="ogrn", type="string", length=255, nullable=true)
     */
    private $ogrn;

    /**
     * ФИО руководителя Юр. лица
     * @ORM\Column(name="boss_name", type="string", length=255, nullable=true)
     */
    private $bossName;

    /**
     * Должность руководителя Юр. лица
     * @ORM\Column(name="boss_position", type="string", length=255, nullable=true)
     */
    private $bossPosition;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="debtors")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="DebtorType")
     * @ORM\JoinColumn(name="debtor_type_id", referencedColumnName="id", nullable=false)
     */
    private $debtorType;

    /**
     * @ORM\ManyToOne(targetEntity="OwnershipStatus")
     * @ORM\JoinColumn(name="ownership_status_id", referencedColumnName="id")
     */
    private $ownershipStatus;

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
     * Set location
     *
     * @param string $location
     *
     * @return Debtor
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set startDateOwnership
     *
     * @param \DateTime $startDateOwnership
     *
     * @return Debtor
     */
    public function setStartDateOwnership($startDateOwnership)
    {
        $this->startDateOwnership = $startDateOwnership;

        return $this;
    }

    /**
     * Get startDateOwnership
     *
     * @return \DateTime
     */
    public function getStartDateOwnership()
    {
        return $this->startDateOwnership;
    }

    /**
     * Set endDateOwnership
     *
     * @param \DateTime $endDateOwnership
     *
     * @return Debtor
     */
    public function setEndDateOwnership($endDateOwnership)
    {
        $this->endDateOwnership = $endDateOwnership;

        return $this;
    }

    /**
     * Get endDateOwnership
     *
     * @return \DateTime
     */
    public function getEndDateOwnership()
    {
        return $this->endDateOwnership;
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
     * Set dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     *
     * @return Debtor
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set placeOfBirth
     *
     * @param string $placeOfBirth
     *
     * @return Debtor
     */
    public function setPlaceOfBirth($placeOfBirth)
    {
        $this->placeOfBirth = $placeOfBirth;

        return $this;
    }

    /**
     * Get placeOfBirth
     *
     * @return string
     */
    public function getPlaceOfBirth()
    {
        return $this->placeOfBirth;
    }

    /**
     * Set ownerName
     *
     * @param string $ownerName
     *
     * @return Debtor
     */
    public function setOwnerName($ownerName)
    {
        $this->ownerName = $ownerName;

        return $this;
    }

    /**
     * Get ownerName
     *
     * @return string
     */
    public function getOwnerName()
    {
        return $this->ownerName;
    }

    /**
     * Set ogrnip
     *
     * @param string $ogrnip
     *
     * @return Debtor
     */
    public function setOgrnip($ogrnip)
    {
        $this->ogrnip = $ogrnip;

        return $this;
    }

    /**
     * Get ogrnip
     *
     * @return string
     */
    public function getOgrnip()
    {
        return $this->ogrnip;
    }

    /**
     * Set inn
     *
     * @param string $inn
     *
     * @return Debtor
     */
    public function setInn($inn)
    {
        $this->inn = $inn;

        return $this;
    }

    /**
     * Get inn
     *
     * @return string
     */
    public function getInn()
    {
        return $this->inn;
    }

    /**
     * Set ogrn
     *
     * @param string $ogrn
     *
     * @return Debtor
     */
    public function setOgrn($ogrn)
    {
        $this->ogrn = $ogrn;

        return $this;
    }

    /**
     * Get ogrn
     *
     * @return string
     */
    public function getOgrn()
    {
        return $this->ogrn;
    }

    /**
     * Set bossName
     *
     * @param string $bossName
     *
     * @return Debtor
     */
    public function setBossName($bossName)
    {
        $this->bossName = $bossName;

        return $this;
    }

    /**
     * Get bossName
     *
     * @return string
     */
    public function getBossName()
    {
        return $this->bossName;
    }

    /**
     * Set bossPosition
     *
     * @param string $bossPosition
     *
     * @return Debtor
     */
    public function setBossPosition($bossPosition)
    {
        $this->bossPosition = $bossPosition;

        return $this;
    }

    /**
     * Get bossPosition
     *
     * @return string
     */
    public function getBossPosition()
    {
        return $this->bossPosition;
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

    /**
     * Set debtorType
     *
     * @param \AppBundle\Entity\DebtorType $debtorType
     *
     * @return Debtor
     */
    public function setDebtorType(\AppBundle\Entity\DebtorType $debtorType)
    {
        $this->debtorType = $debtorType;

        return $this;
    }

    /**
     * Get debtorType
     *
     * @return \AppBundle\Entity\DebtorType
     */
    public function getDebtorType()
    {
        return $this->debtorType;
    }

    /**
     * Set ownershipStatus
     *
     * @param \AppBundle\Entity\OwnershipStatus $ownershipStatus
     *
     * @return Debtor
     */
    public function setOwnershipStatus(\AppBundle\Entity\OwnershipStatus $ownershipStatus = null)
    {
        $this->ownershipStatus = $ownershipStatus;

        return $this;
    }

    /**
     * Get ownershipStatus
     *
     * @return \AppBundle\Entity\OwnershipStatus
     */
    public function getOwnershipStatus()
    {
        return $this->ownershipStatus;
    }
}
