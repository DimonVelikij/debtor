<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="logs")
 *
 * @JMS\ExclusionPolicy("all")
 *
 * Class Log
 * @package AppBundle\Entity
 */
class Log
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\SerializedName("id")
     * @JMS\Groups({"cms-log"})
     */
    private $id;

    /**
     * @ORM\Column(name="date", type="datetime", nullable=false)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("date")
     * @JMS\Accessor(getter="getDateString")
     * @JMS\Groups({"cms-log"})
     */
    private $date;

    /**
     * @ORM\Column(name="data", type="text", nullable=false)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("data")
     * @JMS\Groups({"cms-log"})
     */
    private $data;

    /**
     * @ORM\ManyToOne(targetEntity="PersonalAccount", inversedBy="logs")
     * @ORM\JoinColumn(name="personal_account_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $personalAccount;

    /**
     * @return null|string
     */
    public function getDateString()
    {
        if ($this->date instanceof \DateTime) {
            return $this->date->format('dmYHi');
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Log
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
     * Set data
     *
     * @param string $data
     *
     * @return Log
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set personalAccount
     *
     * @param \AppBundle\Entity\PersonalAccount $personalAccount
     *
     * @return Log
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
