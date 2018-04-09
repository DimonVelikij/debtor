<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="ownership_statuses")
 *
 * @JMS\ExclusionPolicy("all")
 *
 * Class OwnershipStatus
 * @package AppBundle\Entity
 */
class OwnershipStatus
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\SerializedName("id")
     * @JMS\Groups({"cms-debtor"})
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("title")
     * @JMS\Groups({"cms-debtor"})
     */
    private $title;

    /**
     * @ORM\Column(name="alias", type="string", length=255, nullable=false)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("alias")
     * @JMS\Groups({"cms-debtor"})
     */
    private $alias;

    /**
     * @ORM\OneToMany(targetEntity="OwnershipStatus", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="OwnershipStatus", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title ?: '';
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
     * @return OwnershipStatus
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
     * Set alias
     *
     * @param string $alias
     *
     * @return OwnershipStatus
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\OwnershipStatus $child
     *
     * @return OwnershipStatus
     */
    public function addChild(\AppBundle\Entity\OwnershipStatus $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\OwnershipStatus $child
     */
    public function removeChild(\AppBundle\Entity\OwnershipStatus $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\OwnershipStatus $parent
     *
     * @return OwnershipStatus
     */
    public function setParent(\AppBundle\Entity\OwnershipStatus $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\OwnershipStatus
     */
    public function getParent()
    {
        return $this->parent;
    }
}
