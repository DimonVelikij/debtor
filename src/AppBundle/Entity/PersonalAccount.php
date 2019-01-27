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
     * @ORM\Column(name="date_open_account", type="date", nullable=true)
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
     * @ORM\Column(name="generate_errors", type="string", length=255, nullable=true)
     */
    private $generateErrors;

    /**
     * @ORM\Column(name="event_data", type="object", nullable=true)
     */
    private $eventData;

    /**
     * @ORM\ManyToOne(targetEntity="Flat", inversedBy="personalAccounts")
     * @ORM\JoinColumn(name="flat_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $flat;

    /**
     * @ORM\OneToMany(targetEntity="Log", mappedBy="personalAccount")
     */
    private $logs;

    /**
     * @ORM\OneToMany(targetEntity="Subscriber", mappedBy="personalAccount")
     */
    private $subscribers;

    /**
     * @ORM\OneToMany(targetEntity="Debtor", mappedBy="personalAccount")
     */
    private $debtors;

    /**
     * @ORM\OneToMany(targetEntity="PaymentHistory", mappedBy="personalAccount")
     */
    private $paymentHistory;

    /**
     * @ORM\OneToMany(targetEntity="PersonalAccountEvent", mappedBy="flat", cascade={"persist"})
     */
    private $personalAccountsEvents;

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
     * Constructor
     */
    public function __construct()
    {
        $this->logs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->subscribers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->debtors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->paymentHistory = new \Doctrine\Common\Collections\ArrayCollection();
        $this->personalAccountsEvents = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set generateErrors
     *
     * @param string $generateErrors
     *
     * @return PersonalAccount
     */
    public function setGenerateErrors($generateErrors)
    {
        $this->generateErrors = $generateErrors;

        return $this;
    }

    /**
     * Get generateErrors
     *
     * @return string
     */
    public function getGenerateErrors()
    {
        return $this->generateErrors;
    }

    /**
     * Set eventData
     *
     * @param \stdClass $eventData
     *
     * @return PersonalAccount
     */
    public function setEventData($eventData)
    {
        $this->eventData = $eventData;

        return $this;
    }

    /**
     * Get eventData
     *
     * @return \stdClass
     */
    public function getEventData()
    {
        return $this->eventData;
    }

    /**
     * Set flat
     *
     * @param \AppBundle\Entity\Flat $flat
     *
     * @return PersonalAccount
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
     * Add log
     *
     * @param \AppBundle\Entity\Log $log
     *
     * @return PersonalAccount
     */
    public function addLog(\AppBundle\Entity\Log $log)
    {
        $this->logs[] = $log;

        return $this;
    }

    /**
     * Remove log
     *
     * @param \AppBundle\Entity\Log $log
     */
    public function removeLog(\AppBundle\Entity\Log $log)
    {
        $this->logs->removeElement($log);
    }

    /**
     * Get logs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Add subscriber
     *
     * @param \AppBundle\Entity\Subscriber $subscriber
     *
     * @return PersonalAccount
     */
    public function addSubscriber(\AppBundle\Entity\Subscriber $subscriber)
    {
        $this->subscribers[] = $subscriber;

        return $this;
    }

    /**
     * Remove subscriber
     *
     * @param \AppBundle\Entity\Subscriber $subscriber
     */
    public function removeSubscriber(\AppBundle\Entity\Subscriber $subscriber)
    {
        $this->subscribers->removeElement($subscriber);
    }

    /**
     * Get subscribers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * Add debtor
     *
     * @param \AppBundle\Entity\Debtor $debtor
     *
     * @return PersonalAccount
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

    /**
     * Add paymentHistory
     *
     * @param \AppBundle\Entity\PaymentHistory $paymentHistory
     *
     * @return PersonalAccount
     */
    public function addPaymentHistory(\AppBundle\Entity\PaymentHistory $paymentHistory)
    {
        $this->paymentHistory[] = $paymentHistory;

        return $this;
    }

    /**
     * Remove paymentHistory
     *
     * @param \AppBundle\Entity\PaymentHistory $paymentHistory
     */
    public function removePaymentHistory(\AppBundle\Entity\PaymentHistory $paymentHistory)
    {
        $this->paymentHistory->removeElement($paymentHistory);
    }

    /**
     * Get paymentHistory
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentHistory()
    {
        return $this->paymentHistory;
    }

    /**
     * Add personalAccountsEvent
     *
     * @param \AppBundle\Entity\PersonalAccountEvent $personalAccountsEvent
     *
     * @return PersonalAccount
     */
    public function addPersonalAccountsEvent(\AppBundle\Entity\PersonalAccountEvent $personalAccountsEvent)
    {
        $this->personalAccountsEvents[] = $personalAccountsEvent;

        return $this;
    }

    /**
     * Remove personalAccountsEvent
     *
     * @param \AppBundle\Entity\PersonalAccountEvent $personalAccountsEvent
     */
    public function removePersonalAccountsEvent(\AppBundle\Entity\PersonalAccountEvent $personalAccountsEvent)
    {
        $this->personalAccountsEvents->removeElement($personalAccountsEvent);
    }

    /**
     * Get personalAccountsEvents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPersonalAccountsEvents()
    {
        return $this->personalAccountsEvents;
    }
}
