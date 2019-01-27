<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="personal_accounts_events")
 *
 * Class PersonalAccountEvent
 * @package AppBundle\Entity
 */
class PersonalAccountEvent
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PersonalAccount", inversedBy="personalAccountsEvents")
     * @ORM\JoinColumn(name="personal_account_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $personalAcoount;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $event;

    /**
     * @ORM\Column(name="date_generate", type="datetime", nullable=false)
     */
    private $dateGenerate;

    /**
     * @ORM\Column(name="data", type="object", nullable=true)
     */
    private $data;

    /**
     * @param $parameter
     * @param null $default
     * @return null
     */
    public function getParameter($parameter, $default = false)
    {
        return $this->data[$parameter] ?? $default;
    }

    /**
     * @param $parameter
     * @param $value
     * @return PersonalAccountEvent
     */
    public function setParameter($parameter, $value)
    {
        $this->data[$parameter] = $value;

        return $this;
    }


    /**
     * Set dateGenerate
     *
     * @param \DateTime $dateGenerate
     *
     * @return PersonalAccountEvent
     */
    public function setDateGenerate($dateGenerate)
    {
        $this->dateGenerate = $dateGenerate;

        return $this;
    }

    /**
     * Get dateGenerate
     *
     * @return \DateTime
     */
    public function getDateGenerate()
    {
        return $this->dateGenerate;
    }

    /**
     * Set data
     *
     * @param \stdClass $data
     *
     * @return PersonalAccountEvent
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \stdClass
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set personalAcoount
     *
     * @param \AppBundle\Entity\PersonalAccount $personalAcoount
     *
     * @return PersonalAccountEvent
     */
    public function setPersonalAcoount(\AppBundle\Entity\PersonalAccount $personalAcoount)
    {
        $this->personalAcoount = $personalAcoount;

        return $this;
    }

    /**
     * Get personalAcoount
     *
     * @return \AppBundle\Entity\PersonalAccount
     */
    public function getPersonalAcoount()
    {
        return $this->personalAcoount;
    }

    /**
     * Set event
     *
     * @param \AppBundle\Entity\Event $event
     *
     * @return PersonalAccountEvent
     */
    public function setEvent(\AppBundle\Entity\Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \AppBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }
}
