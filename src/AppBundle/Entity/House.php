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
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="houses")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $company;

    /**
     * @ORM\Column(name="management_start_date", type="date", nullable=false)
     * дата начала управления
     */
    private $managementStartDate;

    /**
     * @ORM\Column(name="management_end_date", type="date", nullable=true)
     * дата окончания управления
     */
    private $managementEndDate;

    /**
     * @ORM\Column(name="legal_document_name", type="string", length=255, nullable=false)
     * название документа на право управления
     */
    private $legalDocumentName;

    /**
     * @ORM\Column(name="legal_document_date", type="date", nullable=false)
     * дата документа начала управления
     */
    private $legalDocumentDate;

    /**
     * @ORM\Column(name="legal_document_number", type="string", length=255, nullable=true)
     * номер документа начала управления
     */
    private $legalDocumentNumber;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JudicialSector")
     * @ORM\JoinColumn(name="judicial_sector_id", referencedColumnName="id", nullable=false)
     */
    private $judicialSector;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FSSPDepartment")
     * @ORM\JoinColumn(name="fssp_department_id", referencedColumnName="id", nullable=false)
     */
    private $fsspDepartment;

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
     * Set managementStartDate
     *
     * @param \DateTime $managementStartDate
     *
     * @return House
     */
    public function setManagementStartDate($managementStartDate)
    {
        $this->managementStartDate = $managementStartDate;

        return $this;
    }

    /**
     * Get managementStartDate
     *
     * @return \DateTime
     */
    public function getManagementStartDate()
    {
        return $this->managementStartDate;
    }

    /**
     * Set managementEndDate
     *
     * @param \DateTime $managementEndDate
     *
     * @return House
     */
    public function setManagementEndDate($managementEndDate)
    {
        $this->managementEndDate = $managementEndDate;

        return $this;
    }

    /**
     * Get managementEndDate
     *
     * @return \DateTime
     */
    public function getManagementEndDate()
    {
        return $this->managementEndDate;
    }

    /**
     * Set legalDocumentName
     *
     * @param string $legalDocumentName
     *
     * @return House
     */
    public function setLegalDocumentName($legalDocumentName)
    {
        $this->legalDocumentName = $legalDocumentName;

        return $this;
    }

    /**
     * Get legalDocumentName
     *
     * @return string
     */
    public function getLegalDocumentName()
    {
        return $this->legalDocumentName;
    }

    /**
     * Set legalDocumentDate
     *
     * @param \DateTime $legalDocumentDate
     *
     * @return House
     */
    public function setLegalDocumentDate($legalDocumentDate)
    {
        $this->legalDocumentDate = $legalDocumentDate;

        return $this;
    }

    /**
     * Get legalDocumentDate
     *
     * @return \DateTime
     */
    public function getLegalDocumentDate()
    {
        return $this->legalDocumentDate;
    }

    /**
     * Set legalDocumentNumber
     *
     * @param string $legalDocumentNumber
     *
     * @return House
     */
    public function setLegalDocumentNumber($legalDocumentNumber)
    {
        $this->legalDocumentNumber = $legalDocumentNumber;

        return $this;
    }

    /**
     * Get legalDocumentNumber
     *
     * @return string
     */
    public function getLegalDocumentNumber()
    {
        return $this->legalDocumentNumber;
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

    /**
     * Set company
     *
     * @param \AppBundle\Entity\Company $company
     *
     * @return House
     */
    public function setCompany(\AppBundle\Entity\Company $company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \AppBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set judicialSector
     *
     * @param \AppBundle\Entity\JudicialSector $judicialSector
     *
     * @return House
     */
    public function setJudicialSector(\AppBundle\Entity\JudicialSector $judicialSector)
    {
        $this->judicialSector = $judicialSector;

        return $this;
    }

    /**
     * Get judicialSector
     *
     * @return \AppBundle\Entity\JudicialSector
     */
    public function getJudicialSector()
    {
        return $this->judicialSector;
    }

    /**
     * Set fsspDepartment
     *
     * @param \AppBundle\Entity\FSSPDepartment $fsspDepartment
     *
     * @return House
     */
    public function setFsspDepartment(\AppBundle\Entity\FSSPDepartment $fsspDepartment)
    {
        $this->fsspDepartment = $fsspDepartment;

        return $this;
    }

    /**
     * Get fsspDepartment
     *
     * @return \AppBundle\Entity\FSSPDepartment
     */
    public function getFsspDepartment()
    {
        return $this->fsspDepartment;
    }
}
