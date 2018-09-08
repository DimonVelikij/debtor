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
     * @ORM\Column(name="is_read", type="boolean", nullable=false)
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("isRead")
     * @JMS\Groups({"cms-log"})
     */
    private $isRead;

    /**
     * @ORM\ManyToOne(targetEntity="Flat", inversedBy="logs")
     * @ORM\JoinColumn(name="flat_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $flat;

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
     * Set isRead
     *
     * @param boolean $isRead
     *
     * @return Log
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * Get isRead
     *
     * @return boolean
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * Set flat
     *
     * @param \AppBundle\Entity\Flat $flat
     *
     * @return Log
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
