<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="subscribers")
 *
 * @JMS\ExclusionPolicy("all")
 *
 * Class Subscriber
 * @package AppBundle\Entity
 */
class Subscriber
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\SerializedName("id")
     * @JMS\Groups({"cms-subscriber"})
     */
    private $id;

    /**
     * ФИО абонента
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("name")
     * @JMS\Groups({"cms-subscriber"})
     */
    private $name;

    /**
     * телефон
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("phone")
     * @JMS\Groups({"cms-subscriber"})
     */
    private $phone;

    /**
     * email
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("email")
     * @JMS\Groups({"cms-subscriber"})
     */
    private $email;

    /**
     * дата заполнения долга
     * @ORM\Column(name="date_debt", type="date", nullable=false)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("dateDebt")
     * @JMS\Accessor(getter="getDateDebtString")
     * @JMS\Groups({"cms-subscriber"})
     */
    private $dateDebt;

    /**
     * сумма долга
     * @ORM\Column(name="sum_debt", type="float", precision=10, scale=2, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("sumDebt")
     * @JMS\Groups({"cms-subscriber"})
     */
    private $sumDebt;

    /**
     * сумма пени
     * @ORM\Column(name="sum_fine", type="float", precision=10, scale=2, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("sumFine")
     * @JMS\Groups({"cms-subscriber"})
     */
    private $sumFine;

    /**
     * @ORM\ManyToOne(targetEntity="Flat", inversedBy="subscribers")
     * @ORM\JoinColumn(name="flat_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $flat;

    /**
     * @return null|string
     */
    public function getDateDebtString()
    {
        if ($this->dateDebt instanceof \DateTime) {
            return $this->dateDebt->format('dmY');
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
     * @return Subscriber
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
     * @return Subscriber
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
     * @return Subscriber
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
     * Set dateDebt
     *
     * @param \DateTime $dateDebt
     *
     * @return Subscriber
     */
    public function setDateDebt($dateDebt)
    {
        $this->dateDebt = $dateDebt;

        return $this;
    }

    /**
     * Get dateDebt
     *
     * @return \DateTime
     */
    public function getDateDebt()
    {
        return $this->dateDebt;
    }

    /**
     * Set sumDebt
     *
     * @param float $sumDebt
     *
     * @return Subscriber
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
     * Set sumFine
     *
     * @param float $sumFine
     *
     * @return Subscriber
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
     * Set flat
     *
     * @param \AppBundle\Entity\Flat $flat
     *
     * @return Subscriber
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
}
