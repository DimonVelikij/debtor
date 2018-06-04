<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="templates")
 *
 * Class Template
 * @package AppBundle\Entity
 */
class Template
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
     * @Gedmo\Slug(fields={"name"}, updatable=false, unique=true)
     * @ORM\Column(length=255, unique=true)
     *
     * slug названия шаблона
     */
    private $slug;

    /**
     * @ORM\Column(name="template", type="text", nullable=true)
     *
     * текст шаблона
     */
    private $template;

    /**
     * @ORM\Column(name="time_perform_action", type="integer", nullable=false)
     *
     * через какое количество дней выполнить генерацию
     */
    private $timePerformAction;

    /**
     * @ORM\Column(name="template_fields", type="object", nullable=true)
     *
     * поля шаблона
     */
    private $templateFields;

    /**
     * @ORM\Column(name="is_start", type="boolean", nullable=true, options={"default":0})
     *
     * является ли стартовым
     */
    private $isStart;

    /**
     * @ORM\Column(name="is_judicial", type="boolean", nullable=true, options={"default":0})
     *
     * является ли судебным
     */
    private $isJudicial;

    /**
     * @ORM\OneToOne(targetEntity="Template")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     *
     * следующий шаблон
     */
    private $parent;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name ?? '';
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
     * @return Template
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Template
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
     * Set template
     *
     * @param string $template
     *
     * @return Template
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
     * Set timePerformAction
     *
     * @param integer $timePerformAction
     *
     * @return Template
     */
    public function setTimePerformAction($timePerformAction)
    {
        $this->timePerformAction = $timePerformAction;

        return $this;
    }

    /**
     * Get timePerformAction
     *
     * @return integer
     */
    public function getTimePerformAction()
    {
        return $this->timePerformAction;
    }

    /**
     * Set templateFields
     *
     * @param \stdClass $templateFields
     *
     * @return Template
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
     * Set isStart
     *
     * @param boolean $isStart
     *
     * @return Template
     */
    public function setIsStart($isStart)
    {
        $this->isStart = $isStart;

        return $this;
    }

    /**
     * Get isStart
     *
     * @return boolean
     */
    public function getIsStart()
    {
        return $this->isStart;
    }

    /**
     * Set isJudicial
     *
     * @param boolean $isJudicial
     *
     * @return Template
     */
    public function setIsJudicial($isJudicial)
    {
        $this->isJudicial = $isJudicial;

        return $this;
    }

    /**
     * Get isJudicial
     *
     * @return boolean
     */
    public function getIsJudicial()
    {
        return $this->isJudicial;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\Template $parent
     *
     * @return Template
     */
    public function setParent(\AppBundle\Entity\Template $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Template
     */
    public function getParent()
    {
        return $this->parent;
    }
}
