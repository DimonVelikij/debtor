<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="mkds")
 *
 * Class MKD
 * @package AppBundle\Entity
 */
class MKD
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Street")
     * @ORM\JoinColumn(name="street_id", referencedColumnName="id", nullable=false)
     */
    private $street;

    /**
     * @ORM\Column(name="house_number", type="string", length=255, nullable=false)
     * номер дома
     */
    private $houseNumber;

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
        return $this->getId() ?
            $this->getStreet()->getCity()->getTitle() . ', ' . $this->getStreet()->getTitle() . ', ' . $this->getHouseNumber() :
            '';
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
     * Set houseNumber
     *
     * @param string $houseNumber
     *
     * @return MKD
     */
    public function setHouseNumber($houseNumber)
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    /**
     * Get houseNumber
     *
     * @return string
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * Set managementStartDate
     *
     * @param \DateTime $managementStartDate
     *
     * @return MKD
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
     * @return MKD
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
     * @return MKD
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
     * @return MKD
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
     * @return MKD
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
     * @return MKD
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
     * Set judicialSector
     *
     * @param \AppBundle\Entity\JudicialSector $judicialSector
     *
     * @return MKD
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
     * @return MKD
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
