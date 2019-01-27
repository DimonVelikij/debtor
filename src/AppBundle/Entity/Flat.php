<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="flats")
 *
 * Class Flat
 * @package AppBundle\Entity
 */
class Flat
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="number", type="string", length=255, nullable=true)
     *
     * номер помещения (квартиры)
     */
    private $number;

    /**
     * @ORM\Column(name="arhive", type="boolean", nullable=true)
     *
     * перестал быть должником
     */
    private $archive;

    /**
     * @ORM\ManyToOne(targetEntity="House", inversedBy="flats")
     * @ORM\JoinColumn(name="house_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $house;

    /**
     * @ORM\ManyToOne(targetEntity="FlatType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="PersonalAccount", mappedBy="flat")
     */
    private $personalAccounts;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ?
            $this->getHouse()->getStreet()->getCity()->getTitle() . ', ' .
            $this->getHouse()->getStreet()->getTitle() . ', ' .
            $this->getHouse()->getNumber() . ($this->number ?: '') :
            '';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->personalAccounts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Flat
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
     * Set archive
     *
     * @param boolean $archive
     *
     * @return Flat
     */
    public function setArchive($archive)
    {
        $this->archive = $archive;

        return $this;
    }

    /**
     * Get archive
     *
     * @return boolean
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * Set house
     *
     * @param \AppBundle\Entity\House $house
     *
     * @return Flat
     */
    public function setHouse(\AppBundle\Entity\House $house)
    {
        $this->house = $house;

        return $this;
    }

    /**
     * Get house
     *
     * @return \AppBundle\Entity\House
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * Set type
     *
     * @param \AppBundle\Entity\FlatType $type
     *
     * @return Flat
     */
    public function setType(\AppBundle\Entity\FlatType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\FlatType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add personalAccount
     *
     * @param \AppBundle\Entity\PersonalAccount $personalAccount
     *
     * @return Flat
     */
    public function addPersonalAccount(\AppBundle\Entity\PersonalAccount $personalAccount)
    {
        $this->personalAccounts[] = $personalAccount;

        return $this;
    }

    /**
     * Remove personalAccount
     *
     * @param \AppBundle\Entity\PersonalAccount $personalAccount
     */
    public function removePersonalAccount(\AppBundle\Entity\PersonalAccount $personalAccount)
    {
        $this->personalAccounts->removeElement($personalAccount);
    }

    /**
     * Get personalAccounts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPersonalAccounts()
    {
        return $this->personalAccounts;
    }
}
