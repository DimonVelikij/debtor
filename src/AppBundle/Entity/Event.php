<?php

namespace AppBundle\Entity;

use AppBundle\EventGenerator\EventType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="events")
 *
 * Class Event
 * @package AppBundle\Entity
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     *
     * название шаблона
     */
    private $name;

    /**
     * @ORM\Column(name="alias", type="string", length=255, nullable=false, unique=true)
     *
     * alias шаблона
     */
    private $alias;

    /**
     * @ORM\Column(name="template", type="text", nullable=true)
     *
     * текст шаблона
     */
    private $template;

    /**
     * @ORM\Column(name="template_fields", type="object", nullable=true)
     *
     * поля шаблона
     */
    private $templateFields;

    /**
     * @ORM\Column(name="type", type="string", columnDefinition="ENUM('pretense', 'judicature', 'performance')")
     */
    private $type;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name ?? '';
    }

    /**
     * @return mixed|null
     */
    public function getTypeTitle()
    {
        return EventType::$types[$this->type] ?? null;
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
     * @return Event
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
     * Set alias
     *
     * @param string $alias
     *
     * @return Event
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
     * Set template
     *
     * @param string $template
     *
     * @return Event
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set templateFields
     *
     * @param \stdClass $templateFields
     *
     * @return Event
     */
    public function setTemplateFields($templateFields)
    {
        $this->templateFields = $templateFields;

        return $this;
    }

    /**
     * Get templateFields
     *
     * @return \stdClass
     */
    public function getTemplateFields()
    {
        return $this->templateFields;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Event
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
