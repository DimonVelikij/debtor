<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="debtors")
 *
 * @JMS\ExclusionPolicy("all")
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
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\SerializedName("id")
     * @JMS\Groups({"cms-debtor"})
     */
    private $id;

    /**
     * ФИО или наименование организации
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("name")
     * @JMS\Groups({"cms-debtor"})
     */
    private $name;

    /**
     * телефон
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("phone")
     * @JMS\Groups({"cms-debtor"})
     */
    private $phone;

    /**
     * email
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("email")
     * @JMS\Groups({"cms-debtor"})
     */
    private $email;

    /**
     * место нахождения или жительства
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("location")
     * @JMS\Groups({"cms-debtor"})
     */
    private $location;

    /**
     * дата начала собственности
     * @ORM\Column(name="start_date_ownership", type="date", nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("startDateOwnership")
     * @JMS\Accessor(getter="getStartDateOwnershipString")
     * @JMS\Groups({"cms-debtor"})
     */
    private $startDateOwnership;

    /**
     * дата конча собственности
     * @ORM\Column(name="end_date_ownership", type="date", nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("endDateOwnership")
     * @JMS\Accessor(getter="getEndDateOwnershipString")
     * @JMS\Groups({"cms-debtor"})
     */
    private $endDateOwnership;

    /**
     * архив
     * @ORM\Column(name="archive", type="boolean", nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("archive")
     * @JMS\Groups({"cms-debtor"})
     */
    private $archive;

    /**
     * размер доли, если статус собственности долевой
     * @ORM\Column(name="share_size", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("shareSize")
     * @JMS\Groups({"cms-debtor"})
     */
    private $shareSize;

    /**
     * дата рождения физ лица
     * @ORM\Column(name="date_of_birth", type="date", nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("dateOfBirth")
     * @JMS\Accessor(getter="getDateOfBirthString")
     * @JMS\Groups({"cms-debtor"})
     */
    private $dateOfBirth;

    /**
     * место рождения физ лица
     * @ORM\Column(name="place_of_birth", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("placeOfBirth")
     * @JMS\Groups({"cms-debtor"})
     */
    private $placeOfBirth;

    /**
     * ФИО собственника, если статус - законный представитель несовершеннолетнего собственника
     * @ORM\Column(name="owner_name", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("ownerName")
     * @JMS\Groups({"cms-debtor"})
     */
    private $ownerName;

    /**
     * ОГРНИП индивидульного предпринимателя
     * @ORM\Column(name="ogrnip", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("ogrnip")
     * @JMS\Groups({"cms-debtor"})
     */
    private $ogrnip;

    /**
     * ИНН индивидуального предпринимателя или Юр. лица
     * @ORM\Column(name="inn", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("inn")
     * @JMS\Groups({"cms-debtor"})
     */
    private $inn;

    /**
     * ОГРН Юр. лица
     * @ORM\Column(name="ogrn", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("ogrn")
     * @JMS\Groups({"cms-debtor"})
     */
    private $ogrn;

    /**
     * ФИО руководителя Юр. лица
     * @ORM\Column(name="boss_name", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("bossName")
     * @JMS\Groups({"cms-debtor"})
     */
    private $bossName;

    /**
     * Должность руководителя Юр. лица
     * @ORM\Column(name="boss_position", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("bossPosition")
     * @JMS\Groups({"cms-debtor"})
     */
    private $bossPosition;

    /**
     * @ORM\ManyToOne(targetEntity="Flat", inversedBy="debtors")
     * @ORM\JoinColumn(name="flat_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $flat;

    /**
     * @ORM\ManyToOne(targetEntity="DebtorType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     *
     * @JMS\Expose
     * @JMS\SerializedName("type")
     * @JMS\Groups({"cms-debtor"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="OwnershipStatus")
     * @ORM\JoinColumn(name="ownership_status_id", referencedColumnName="id", nullable=false)
     *
     * @JMS\Expose
     * @JMS\SerializedName("ownershipStatus")
     * @JMS\Groups({"cms-debtor"})
     */
    private $ownershipStatus;

    /**
     * @ORM\ManyToOne(targetEntity="PersonalAccount", cascade={"persist"})
     * @ORM\JoinColumn(name="personal_account_id", referencedColumnName="id", nullable=false)
     *
     * @JMS\Expose
     * @JMS\SerializedName("personalAccount")
     * @JMS\Groups({"cms-debtor"})
     */
    private $personalAccount;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name ?? '';
    }

    /**
     * @return null|string
     */
    public function getStartDateOwnershipString()
    {
        if ($this->startDateOwnership instanceof \DateTime) {
            return $this->startDateOwnership->format('dmY');
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getEndDateOwnershipString()
    {
        if ($this->endDateOwnership instanceof \DateTime) {
            return $this->endDateOwnership->format('dmY');
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getDateOfBirthString()
    {
        if ($this->dateOfBirth instanceof \DateTime) {
            return $this->dateOfBirth->format('dmY');
        }

        return null;
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
     * Set archive
     *
     * @param boolean $archive
     *
     * @return Debtor
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
     * Set shareSize
     *
     * @param string $shareSize
     *
     * @return Debtor
     */
    public function setShareSize($shareSize)
    {
        $this->shareSize = $shareSize;

        return $this;
    }

    /**
     * Get shareSize
     *
     * @return string
     */
    public function getShareSize()
    {
        return $this->shareSize;
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
     * Set flat
     *
     * @param \AppBundle\Entity\Flat $flat
     *
     * @return Debtor
     */
    public function setFlat(\AppBundle\Entity\Flat $flat)
    {
        $this->flat = $flat;

        return $this;
    }

    /**
     * Get flat
     *
     * @return \AppBundle\Entity\Flat
     */
    public function getFlat()
    {
        return $this->flat;
    }

    /**
     * Set type
     *
     * @param \AppBundle\Entity\DebtorType $type
     *
     * @return Debtor
     */
    public function setType(\AppBundle\Entity\DebtorType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\DebtorType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set ownershipStatus
     *
     * @param \AppBundle\Entity\OwnershipStatus $ownershipStatus
     *
     * @return Debtor
     */
    public function setOwnershipStatus(\AppBundle\Entity\OwnershipStatus $ownershipStatus)
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

    /**
     * Set personalAccount
     *
     * @param \AppBundle\Entity\PersonalAccount $personalAccount
     *
     * @return Debtor
     */
    public function setPersonalAccount(\AppBundle\Entity\PersonalAccount $personalAccount)
    {
        $this->personalAccount = $personalAccount;

        return $this;
    }

    /**
     * Get personalAccount
     *
     * @return \AppBundle\Entity\PersonalAccount
     */
    public function getPersonalAccount()
    {
        return $this->personalAccount;
    }
}
