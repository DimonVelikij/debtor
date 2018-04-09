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
     */
    private $title;

    /**
     * @ORM\Column(name="ogrn", type="string", length=255, nullable=false)
     */
    private $ogrn;

    /**
     * @ORM\Column(name="inn", type="string", length=255, nullable=false)
     */
    private $inn;

    /**
     * @ORM\Column(name="address", type="string", length=255, nullable=false)
     */
    private $address;

    /**
     * @ORM\Column(name="post_address", type="string", length=255, nullable=false)
     */
    private $postAddress;

    /**
     * @ORM\Column(name="phone", type="string", length=255, nullable=false)
     */
    private $phone;

    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="company")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Debtor", mappedBy="company")
     */
    private $debtors;

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
     * Add debtor
     *
     * @param \AppBundle\Entity\Debtor $debtor
     *
     * @return Company
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
