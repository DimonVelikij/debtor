<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="flat_types")
 *
 * @JMS\ExclusionPolicy("all")
 *
 * Class FlatType
 * @package AppBundle\Entity
 */
class FlatType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\SerializedName("id")
     * @JMS\Groups({"cms-flat"})
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("title")
     * @JMS\Groups({"cms-flat"})
     */
    private $title;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title ?? '';
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
     * @return FlatType
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
}
