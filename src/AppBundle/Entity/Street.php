<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="streets")
 *
 * Class Street
 * @package AppBundle\Entity
 */
class Street
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @Gedmo\Slug(fields={"title"}, updatable=false, unique=false)
     * @ORM\Column(length=255, unique=false)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="streets")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity="House", mappedBy="street")
     */
    private $houses;

    /**
     * @ORM\ManyToOne(targetEntity="StreetType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    private $type;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title ? $this->getCity()->getTitle() . ', ' . $this->title : '';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->houses = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Street
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Street
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set city
     *
     * @param \AppBundle\Entity\City $city
     *
     * @return Street
     */
    public function setCity(\AppBundle\Entity\City $city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \AppBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Add house
     *
     * @param \AppBundle\Entity\Street $house
     *
     * @return Street
     */
    public function addHouse(\AppBundle\Entity\Street $house)
    {
        $this->houses[] = $house;

        return $this;
    }

    /**
     * Remove house
     *
     * @param \AppBundle\Entity\Street $house
     */
    public function removeHouse(\AppBundle\Entity\Street $house)
    {
        $this->houses->removeElement($house);
    }

    /**
     * Get houses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHouses()
    {
        return $this->houses;
    }

    /**
     * Set type
     *
     * @param \AppBundle\Entity\StreetType $type
     *
     * @return Street
     */
    public function setType(\AppBundle\Entity\StreetType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\StreetType
     */
    public function getType()
    {
        return $this->type;
    }
}
