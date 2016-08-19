<?php

namespace UKMNorge\TidBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

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
}
