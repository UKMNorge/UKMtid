<?php

namespace UKMNorge\TidBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * Month
 *
 * @ORM\Table(name="month")
 * @ORM\Entity(repositoryClass="UKMNorge\TidBundle\Repository\MonthRepository")
 */
class Month
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
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="months")
     */
    private $user;

    /**
     * 
     *
     * @ORM\ManyToOne(targetEntity="BaseMonth")
     */
    private $month;

    /**
     * @var int
     *
     * @ORM\Column(name="worked", type="integer", nullable=true)
     */
    private $worked;

    // class Month:
    /**
     * @ORM\OneToMany(targetEntity="Interval", mappedBy="month")
     */
    private $intervals;


    public function __construct() {
         $this->intervals = new ArrayCollection();
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
     * Set user
     *
     * @param \stdClass $user
     *
     * @return Month
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \stdClass
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return Month
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set month
     *
     * @return Month
     */
    public function setMonth($baseMonth)
    {
        $this->month = $baseMonth;

        return $this;
    }

    /**
     * Get month
     *
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set worked
     *
     * @param integer $worked
     *
     * @return Month
     */
    public function setWorked($worked)
    {
        $this->worked = $worked;

        return $this;
    }

    /**
     * Get worked
     *
     * @return int
     */
    public function getWorked()
    {
        return $this->worked;
    }

    /**
     * Set intervals
     *
     * @param \stdClass $intervals
     *
     * @return Month
     */
    public function setIntervals($intervals)
    {
        throw new Exception("You cannot set intervals from the inverse side. Use $interval->setMonth() or $month->addInterval instead.");
     #   $this->intervals = $intervals;
      #  return $this;
    }

    /**
     * Get intervals
     *
     * @return \stdClass
     */
    public function getIntervals()
    {
        return $this->intervals;
    }

    /**
     * Add interval
     *
     * @param \UKMNorge\TidBundle\Entity\Interval $interval
     *
     * @return Month
     */
    public function addInterval(\UKMNorge\TidBundle\Entity\Interval $interval)
    {
        $this->intervals[] = $interval;

        return $this;
    }

    /**
     * Remove interval
     *
     * @param \UKMNorge\TidBundle\Entity\Interval $interval
     */
    public function removeInterval(\UKMNorge\TidBundle\Entity\Interval $interval)
    {
        $this->intervals->removeElement($interval);
    }
}
