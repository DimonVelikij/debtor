<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="cities")
 *
 * Class City
 * @package AppBundle\Entity
 */
class City
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
     * @Gedmo\Slug(fields={"title"}, updatable=false, unique=true)
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="Street", mappedBy="city")
     */
    private $streets;

    /**
     * @ORM\Column(name="city_index", type="integer", nullable=true)
     */
    private $cityIndex;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title ?? '';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->streets = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return City
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
     * @return City
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
     * Set cityIndex
     *
     * @param integer $cityIndex
     *
     * @return City
     */
    public function setCityIndex($cityIndex)
    {
        $this->cityIndex = $cityIndex;

        return $this;
    }

    /**
     * Get cityIndex
     *
     * @return integer
     */
    public function getCityIndex()
    {
        return $this->cityIndex;
    }

    /**
     * Add street
     *
     * @param \AppBundle\Entity\Street $street
     *
     * @return City
     */
    public function addStreet(\AppBundle\Entity\Street $street)
    {
        $this->streets[] = $street;

        return $this;
    }

    /**
     * Remove street
     *
     * @param \AppBundle\Entity\Street $street
     */
    public function removeStreet(\AppBundle\Entity\Street $street)
    {
        $this->streets->removeElement($street);
    }

    /**
     * Get streets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStreets()
    {
        return $this->streets;
    }
}
