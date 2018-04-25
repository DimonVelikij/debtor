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
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * email
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * место нахождения или жительства
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
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
     * архив
     * @ORM\Column(name="archive", type="boolean", nullable=true)
     */
    private $archive;

    /**
     * является ли абонентом
     * @ORM\Column(name="subscriber", type="boolean", nullable=true)
     */
    private $subscriber;

    /**
     * размер доли, если статус собственности долевой
     * @ORM\Column(name="share_size", type="string", length=255, nullable=true)
     */
    private $shareSize;

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
     * @ORM\ManyToOne(targetEntity="Flat", inversedBy="debtors")
     * @ORM\JoinColumn(name="flat_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $flat;

    /**
     * @ORM\ManyToOne(targetEntity="DebtorType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    private $type;

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
     * Set subscriber
     *
     * @param boolean $subscriber
     *
     * @return Debtor
     */
    public function setSubscriber($subscriber)
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    /**
     * Get subscriber
     *
     * @return boolean
     */
    public function getSubscriber()
    {
        return $this->subscriber;
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
}
