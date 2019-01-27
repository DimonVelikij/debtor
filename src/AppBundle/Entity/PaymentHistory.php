<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment_history")
 *
 * Class PaymentHistory
 * @package AppBundle\Entity
 */
class PaymentHistory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @ORM\Column(name="debt", type="float", precision=10, scale=2, nullable=false)
     */
    private $debt;

    /**
     * @ORM\Column(name="fine", type="float", precision=10, scale=2, nullable=false)
     */
    private $fine;

    /**
     * @ORM\ManyToOne(targetEntity="PersonalAccount", inversedBy="paymentHistory")
     * @ORM\JoinColumn(name="personal_account_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $personalAccount;

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return PaymentHistory
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set debt
     *
     * @param float $debt
     *
     * @return PaymentHistory
     */
    public function setDebt($debt)
    {
        $this->debt = $debt;

        return $this;
    }

    /**
     * Get debt
     *
     * @return float
     */
    public function getDebt()
    {
        return $this->debt;
    }

    /**
     * Set fine
     *
     * @param float $fine
     *
     * @return PaymentHistory
     */
    public function setFine($fine)
    {
        $this->fine = $fine;

        return $this;
    }

    /**
     * Get fine
     *
     * @return float
     */
    public function getFine()
    {
        return $this->fine;
    }

    /**
     * Set personalAccount
     *
     * @param \AppBundle\Entity\PersonalAccount $personalAccount
     *
     * @return PaymentHistory
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
