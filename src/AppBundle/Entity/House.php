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
     * @return string
     */
    public function __toString()
    {
        return $this->number ? $this->getStreet()->getCity()->getTitle() . ', ' . $this->getStreet()->getTitle() . ', ' . $this->number : '';
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
}
