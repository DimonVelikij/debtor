<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="flats_events")
 *
 * Class FlatEvent
 * @package AppBundle\Entity
 */
class FlatEvent
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Flat", inversedBy="flatsEvents")
     * @ORM\JoinColumn(name="flat_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $flat;

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
     * @param $name
     * @param bool $default
     * @return bool
     */
    public function getParameter($name, $default = false)
    {
        return $this->data[$name] ?? $default;
    }

    /**
     * @param $name
     * @param $data
     * @return FlatEvent
     */
    public function addData($name, $data)
    {
        $this->data[$name] = $data;

        return $this;
    }

    /**
     * Set dateGenerate
     *
     * @param \DateTime $dateGenerate
     *
     * @return FlatEvent
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
     * @return FlatEvent
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
     * Set flat
     *
     * @param \AppBundle\Entity\Flat $flat
     *
     * @return FlatEvent
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
     * Set event
     *
     * @param \AppBundle\Entity\Event $event
     *
     * @return FlatEvent
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
