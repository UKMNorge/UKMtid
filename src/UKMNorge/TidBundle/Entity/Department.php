<?php

namespace UKMNorge\TidBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Department
 *
 * @ORM\Table(name="department")
 * @ORM\Entity(repositoryClass="UKMNorge\TidBundle\Repository\DepartmentRepository")
 */
class Department
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="department")
     */
    private $members;

    public function __construct() {
         $this->members = new ArrayCollection();
    }

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
     * @return Department
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
     * Set members
     *
     *
     * @return Department
     */
    public function setMembers($members)
    {
        $this->members = $members;

        return $this;
    }

    /**
     * Get members
     *
     * @return \UKMNorge\UKMTidBundle\Entity\User
     */
    public function getMembers()
    {
        return $this->members->getValues();
    }

    /**
     * Add member
     *
     * @param \UKMNorge\TidBundle\Entity\User $member
     *
     * @return Department
     */
    public function addMember(\UKMNorge\TidBundle\Entity\User $member)
    {
        $this->members[] = $member;

        return $this;
    }

    /**
     * Remove member
     *
     * @param \UKMNorge\TidBundle\Entity\User $member
     */
    public function removeMember(\UKMNorge\TidBundle\Entity\User $member)
    {
        $this->members->removeElement($member);
    }

    /**
     * Add leader
     *
     * @param \UKMNorge\TidBundle\Entity\User $leader
     *
     * @return Department
     */
    public function addLeader(\UKMNorge\TidBundle\Entity\User $leader)
    {
        $this->leaders[] = $leader;

        return $this;
    }

    /**
     * Remove leader
     *
     * @param \UKMNorge\TidBundle\Entity\User $leader
     */
    public function removeLeader(\UKMNorge\TidBundle\Entity\User $leader)
    {
        $this->leaders->removeElement($leader);
    }

    /**
     * Get leaders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLeaders()
    {
        return $this->leaders;
    }
}
