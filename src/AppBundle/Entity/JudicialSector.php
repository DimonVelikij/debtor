<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="judicial_sectors")
 *
 * Class JudicialSector
 * @package AppBundle\Entity
 */
class JudicialSector
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(name="address", type="string", length=255, nullable=false)
     */
    private $address;

    /**
     * @ORM\Column(name="requisites", type="text", nullable=true)
     */
    private $requisites;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?? '';
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
     * @return JudicialSector
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
     * Set address
     *
     * @param string $address
     *
     * @return JudicialSector
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
     * Set requisites
     *
     * @param string $requisites
     *
     * @return JudicialSector
     */
    public function setRequisites($requisites)
    {
        $this->requisites = $requisites;

        return $this;
    }

    /**
     * Get requisites
     *
     * @return string
     */
    public function getRequisites()
    {
        return $this->requisites;
    }
}
