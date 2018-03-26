<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 *
 * Class User
 * @package AppBundle\Entity
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="full_name", type="string", length=255, nullable=true)
     */
    private $fullName;

    /**
     * @ORM\OneToOne(targetEntity="Company", inversedBy="user")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=true)
     */
    private $company;

    /** @var  string */
    protected $oldPassword;

    /** @var  string */
    protected $newPassword;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return !$this->getFullName() ?
            (string)$this->getUsername() :
            (string)$this->getFullName();
    }

    /**
     * @param $fullName
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set company
     *
     * @param \AppBundle\Entity\Company $company
     *
     * @return User
     */
    public function setCompany(\AppBundle\Entity\Company $company = null)
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
     * последняя авторизация для вывода в панели администратора
     * @return string
     */
    public function getLastLoginFromAdmin()
    {
        if (!$this->lastLogin) {
            return '';
        }

        return $this->lastLogin->format('d.m.Y H:i');
    }

    /**
     * название роли в виде строки
     * @return string
     */
    public function getUserRole()
    {
        return $this->isSuperAdmin() ? 'ROLE_SUPER_ADMIN' : 'ROLE_ADMIN';
    }

    /**
     * @param $oldPassword
     * @return User
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * @param $newPassword
     * @return User
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }
}
