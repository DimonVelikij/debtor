<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="houses")
 *
 * Class House
 * @package AppBundle\Entity
 */
class House
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="number", type="string", length=255, nullable=false)
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity="Street", inversedBy="houses")
     * @ORM\JoinColumn(name="street_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $street;

    /**
     * @ORM\OneToMany(targetEntity="Flat", mappedBy="house")
     */
    private $flats;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->number ? $this->getStreet()->getCity()->getTitle() . ', ' . $this->getStreet()->getTitle() . ', ' . $this->number : '';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->flats = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set number
     *
     * @param string $number
     *
     * @return House
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set street
     *
     * @param \AppBundle\Entity\Street $street
     *
     * @return House
     */
    public function setStreet(\AppBundle\Entity\Street $street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return \AppBundle\Entity\Street
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Add flat
     *
     * @param \AppBundle\Entity\Flat $flat
     *
     * @return House
     */
    public function addFlat(\AppBundle\Entity\Flat $flat)
    {
        $this->flats[] = $flat;

        return $this;
    }

    /**
     * Remove flat
     *
     * @param \AppBundle\Entity\Flat $flat
     */
    public function removeFlat(\AppBundle\Entity\Flat $flat)
    {
        $this->flats->removeElement($flat);
    }

    /**
     * Get flats
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFlats()
    {
        return $this->flats;
    }
}
