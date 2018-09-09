<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="personal_accounts")
 *
 * Class PersonalAccount
 * @package AppBundle\Entity
 */
class PersonalAccount
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\SerializedName("id")
     * @JMS\Groups({"cms-subscriber", "cms-debtor"})
     */
    private $id;

    /**
     * @ORM\Column(name="account", type="string", length=255, unique=true, nullable=false)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("account")
     * @JMS\Groups({"cms-subscriber", "cms-debtor"})
     */
    private $account;

    /**
     * дата открытия лицевого счета
     * @ORM\Column(name="date_open_account", type="date", nullable=false)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("dateOpenAccount")
     * @JMS\Accessor(getter="getDateOpenAccountString")
     * @JMS\Groups({"cms-subscriber", "cms-debtor"})
     */
    private $dateOpenAccount;

    /**
     * дата закрытия лицевого счета
     * @ORM\Column(name="date_close_account", type="date", nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("dateCloseAccount")
     * @JMS\Accessor(getter="getDateCloseAccountString")
     * @JMS\Groups({"cms-subscriber", "cms-debtor"})
     */
    private $dateCloseAccount;

    /**
     * @return null|string
     */
    public function getDateOpenAccountString()
    {
        if ($this->dateOpenAccount instanceof \DateTime) {
            return $this->dateOpenAccount->format('dmY');
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getDateCloseAccountString()
    {
        if ($this->dateCloseAccount instanceof \DateTime) {
            return $this->dateCloseAccount->format('dmY');
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
     * Set account
     *
     * @param string $account
     *
     * @return PersonalAccount
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return string
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set dateOpenAccount
     *
     * @param \DateTime $dateOpenAccount
     *
     * @return PersonalAccount
     */
    public function setDateOpenAccount($dateOpenAccount)
    {
        $this->dateOpenAccount = $dateOpenAccount;

        return $this;
    }

    /**
     * Get dateOpenAccount
     *
     * @return \DateTime
     */
    public function getDateOpenAccount()
    {
        return $this->dateOpenAccount;
    }

    /**
     * Set dateCloseAccount
     *
     * @param \DateTime $dateCloseAccount
     *
     * @return PersonalAccount
     */
    public function setDateCloseAccount($dateCloseAccount)
    {
        $this->dateCloseAccount = $dateCloseAccount;

        return $this;
    }

    /**
     * Get dateCloseAccount
     *
     * @return \DateTime
     */
    public function getDateCloseAccount()
    {
        return $this->dateCloseAccount;
    }
}
