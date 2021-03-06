<?php

namespace UKMNorge\TidBundle\Entity;

//use FOS\UserBundle\Model\User as BaseUser;
use UKMNorge\UKMDipBundle\Entity\UserClass as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\OneToMany;
use Doctrine\ORM\ManyToOne;

/**
 * User
 *
 * @ORM\Table(name="tid_user")
 * @ORM\Entity(repositoryClass="UKMNorge\TidBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var int
     *
     * Stillingsprosent.
     *
     * @ORM\Column(name="percentage", type="integer", nullable=true)
     */
    private $percentage;

    /** 
     * @ORM\OneToMany(targetEntity="Month", mappedBy="user")
     */
    private $months;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="members")
     */    
    private $department;

    /**
     *
     * @ORM\Column(name="excludeHolidays", type="boolean", nullable=true)
     */ 
    private $excludeHolidays = false;

    /**
     * Get id
     *
     * @return int
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
     * @return User
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
     * Promote/demote via FOSUserBundle Console commands
     */
    public function isDepartmentManager() {
        if(in_array('ROLE_ADMIN', $this->roles)) {
            return true;
        }
        return false;
    }

    /**
     * Promote/demote via FOSUserBundle Console commands
     */
    public function isSuperUser() {
        if(in_array('ROLE_SUPER_ADMIN', $this->roles)) {
            return true;
        }
        return false;
    }

    /**
     * Set percentage
     *
     * @param integer $percentage
     *
     * @return User
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * Get percentage
     *
     * @return int
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * Add month
     *
     * @param \UKMNorge\TidBundle\Entity\Month $month
     *
     * @return User
     */
    public function addMonth(\UKMNorge\TidBundle\Entity\Month $month)
    {
        $this->months[] = $month;

        return $this;
    }

    /**
     * Remove month
     *
     * @param \UKMNorge\TidBundle\Entity\Month $month
     */
    public function removeMonth(\UKMNorge\TidBundle\Entity\Month $month)
    {
        $this->months->removeElement($month);
    }

    /**
     * Get months
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMonths()
    {
        return $this->months;
    }

    /**
     * Set department
     *
     * @param \UKMNorge\TidBundle\Entity\Department $department
     *
     * @return User
     */
    public function setDepartment(\UKMNorge\TidBundle\Entity\Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \UKMNorge\TidBundle\Entity\Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    // TODO: Implement more?
    // Tilgjengelige datafelt er hentet fra https://github.com/UKMNorge/UKMdelta/blob/master/src/UKMNorge/UserBundle/Security/Authentication/Handler/LoginSuccessHandler.php
    public function setData($data)
    {
        try {
            $this->setFirstName($data->first_name);
            $this->setLastName($data->last_name);
            $this->setName($data->first_name . ' ' . $data->last_name);
        } catch (Exception $e) {
            throw new Exception('UKMTidBundle:User.php:setData: En uventet feil oppsto ved lagring av data fra UKMdelta. ' . $e->getMessage());
        }
    }

    /**
     * Set excludeHolidays
     *
     * @param boolean $excludeHolidays
     *
     * @return User
     */
    public function setExcludeHolidays($excludeHolidays)
    {
        $this->excludeHolidays = $excludeHolidays;

        return $this;
    }

    /**
     * Get excludeHolidays
     *
     * @return boolean
     */
    public function getExcludeHolidays()
    {
        return $this->excludeHolidays;
    }
}
