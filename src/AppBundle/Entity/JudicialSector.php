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
    //ids типов судов
    const DISTRICT = 1;
    const MAGISTRATE = 2;
    const ARBITRATION = 3;

    //названия типов судов
    const DISTRICT_TITLE = 'Районный';
    const MAGISTRATE_TITLE = 'Мировой';
    const ARBITRATION_TITLE = 'Арбитражный';

    //типы событий по ids
    public static $types= [
        self::DISTRICT      => self::DISTRICT_TITLE,
        self::MAGISTRATE    => self::MAGISTRATE_TITLE,
        self::ARBITRATION   =>  self::ARBITRATION_TITLE
    ];

    //список типов судов для сонаты
    public static $sonataTypeChoice = [
        self::DISTRICT_TITLE    => self::DISTRICT,
        self::MAGISTRATE_TITLE  => self::MAGISTRATE,
        self::ARBITRATION_TITLE => self::ARBITRATION
    ];

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
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private $type;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?? '';
    }

    /**
     * название типа
     * @return mixed|string
     */
    public function getTypeTitle()
    {
        return self::$types[$this->getType()] ?? '';
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

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return JudicialSector
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }
}
