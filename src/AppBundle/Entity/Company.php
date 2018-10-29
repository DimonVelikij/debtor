<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="companies")
 *
 * @JMS\ExclusionPolicy("all")
 *
 * Class Company
 * @package AppBundle\Entity
 */
class Company
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
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("title")
     * @JMS\Groups({"cms-debtor"})
     *
     * название
     */
    private $title;

    /**
     * @ORM\Column(name="ogrn", type="string", length=255, nullable=false)
     *
     * ОГРН
     */
    private $ogrn;

    /**
     * @ORM\Column(name="inn", type="string", length=255, nullable=false)
     *
     * ИНН
     */
    private $inn;

    /**
     * @ORM\Column(name="address", type="string", length=255, nullable=false)
     *
     * адрес
     */
    private $address;

    /**
     * @ORM\Column(name="post_address", type="string", length=255, nullable=false)
     *
     * почтовый адрес
     */
    private $postAddress;

    /**
     * @ORM\Column(name="phone", type="string", length=255, nullable=false)
     *
     * телефон
     */
    private $phone;

    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     *
     * e-mail
     */
    private $email;

    /**
     * @ORM\Column(name="checking_account", type="string", length=255, nullable=false)
     *
     * расчетный счет
     */
    private $checkingAccount;

    /**
     * @ORM\Column(name="bank_name", type="string", length=255, nullable=false)
     *
     * наименование банка
     */
    private $bankName;

    /**
     * @ORM\Column(name="bik", type="string", length=255, nullable=false)
     *
     * БИК
     */
    private $bik;

    /**
     * @ORM\Column(name="correspondent_account", type="string", length=255, nullable=false)
     *
     * корреспондентский счет
     */
    private $correspondentAccount;

    /**
     * @ORM\Column(name="director_name", type="string", length=255, nullable=false)
     *
     * ФИО директора
     */
    private $directorName;

    /**
     * @ORM\Column(name="director_position", type="string", length=255, nullable=false)
     *
     * Должность директора
     */
    private $directorPosition;

    /**
     * @ORM\Column(name="director_document", type="string", length=255, nullable=false)
     *
     * Документ, подтверждающий полномочия
     */
    private $directorDocument;

    /**
     * @ORM\Column(name="signature", type="object", nullable=true)
     *
     * подписанты
     */
    private $signature;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="company")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="House", mappedBy="company")
     */
    private $houses;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title ?: '';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->houses = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Company
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set ogrn
     *
     * @param string $ogrn
     *
     * @return Company
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
     * Set inn
     *
     * @param string $inn
     *
     * @return Company
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
     * Set address
     *
     * @param string $address
     *
     * @return Company
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set postAddress
     *
     * @param string $postAddress
     *
     * @return Company
     */
    public function setPostAddress($postAddress)
    {
        $this->postAddress = $postAddress;

        return $this;
    }

    /**
     * Get postAddress
     *
     * @return string
     */
    public function getPostAddress()
    {
        return $this->postAddress;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Company
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
     * @return Company
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
     * Set checkingAccount
     *
     * @param string $checkingAccount
     *
     * @return Company
     */
    public function setCheckingAccount($checkingAccount)
    {
        $this->checkingAccount = $checkingAccount;

        return $this;
    }

    /**
     * Get checkingAccount
     *
     * @return string
     */
    public function getCheckingAccount()
    {
        return $this->checkingAccount;
    }

    /**
     * Set bankName
     *
     * @param string $bankName
     *
     * @return Company
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;

        return $this;
    }

    /**
     * Get bankName
     *
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * Set bik
     *
     * @param string $bik
     *
     * @return Company
     */
    public function setBik($bik)
    {
        $this->bik = $bik;

        return $this;
    }

    /**
     * Get bik
     *
     * @return string
     */
    public function getBik()
    {
        return $this->bik;
    }

    /**
     * Set correspondentAccount
     *
     * @param string $correspondentAccount
     *
     * @return Company
     */
    public function setCorrespondentAccount($correspondentAccount)
    {
        $this->correspondentAccount = $correspondentAccount;

        return $this;
    }

    /**
     * Get correspondentAccount
     *
     * @return string
     */
    public function getCorrespondentAccount()
    {
        return $this->correspondentAccount;
    }

    /**
     * Set directorName
     *
     * @param string $directorName
     *
     * @return Company
     */
    public function setDirectorName($directorName)
    {
        $this->directorName = $directorName;

        return $this;
    }

    /**
     * Get directorName
     *
     * @return string
     */
    public function getDirectorName()
    {
        return $this->directorName;
    }

    /**
     * Set directorPosition
     *
     * @param string $directorPosition
     *
     * @return Company
     */
    public function setDirectorPosition($directorPosition)
    {
        $this->directorPosition = $directorPosition;

        return $this;
    }

    /**
     * Get directorPosition
     *
     * @return string
     */
    public function getDirectorPosition()
    {
        return $this->directorPosition;
    }

    /**
     * Set directorDocument
     *
     * @param string $directorDocument
     *
     * @return Company
     */
    public function setDirectorDocument($directorDocument)
    {
        $this->directorDocument = $directorDocument;

        return $this;
    }

    /**
     * Get directorDocument
     *
     * @return string
     */
    public function getDirectorDocument()
    {
        return $this->directorDocument;
    }

    /**
     * Set signature
     *
     * @param \stdClass $signature
     *
     * @return Company
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get signature
     *
     * @return \stdClass
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Company
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add house
     *
     * @param \AppBundle\Entity\House $house
     *
     * @return Company
     */
    public function addHouse(\AppBundle\Entity\House $house)
    {
        $this->houses[] = $house;

        return $this;
    }

    /**
     * Remove house
     *
     * @param \AppBundle\Entity\House $house
     */
    public function removeHouse(\AppBundle\Entity\House $house)
    {
        $this->houses->removeElement($house);
    }

    /**
     * Get houses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHouses()
    {
        return $this->houses;
    }
}
