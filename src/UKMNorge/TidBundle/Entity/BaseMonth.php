<?php

namespace UKMNorge\TidBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * BaseMonth
 *
 * @ORM\Table(name="base_month")
 * @ORM\Entity(repositoryClass="UKMNorge\TidBundle\Repository\BaseMonthRepository")
 */
class BaseMonth
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
     * @var int
     *
     * @ORM\Column(name="month", type="integer")
     */
    private $month;

    /**
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     *
     * @ORM\OneToMany(targetEntity="Holiday", mappedBy="month")
     */
    private $holidays;

    /**
     * @var int
     *
     * @ORM\Column(name="weekdays", type="integer", nullable=true)
     */
    private $weekdays;

    /**
     *
     *
     * @ORM\Column(name="holidayMinutes", type="integer", nullable=true)
     */
    private $holidayMinutes;

    /**
     * @var int
     *
     * @ORM\Column(name="toWork", type="integer", nullable=true)
     */
    private $toWork;

    public function __construct() {
        $this->holidays = new ArrayCollection();
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
     * Set month
     *
     * @param integer $month
     *
     * @return BaseMonth
     */
    public function setMonth($month)
    {
        $this->month = $month;

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
     * Set year
     *
     * @param integer $year
     *
     * @return BaseMonth
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
     * Set holidays
     *
     * @param \stdClass $holidays
     *
     * @return BaseMonth
     */
    public function setHolidays($holidays)
    {
        $this->holidays = $holidays;

        return $this;
    }

    /**
     * Get holidays
     *
     * @return \stdClass
     */
    public function getHolidays()
    {
        return $this->holidays;
    }

    /**
     * Set weekdays
     *
     * @param integer $weekdays
     *
     * @return BaseMonth
     */
    public function setWeekdays($weekdays)
    {
        $this->weekdays = $weekdays;

        return $this;
    }

    /**
     * Get weekdays
     *
     * @return int
     */
    public function getWeekdays()
    {
        return $this->weekdays;
    }

    /**
     * Set toWork
     *
     * @param integer $toWork
     *
     * @return BaseMonth
     */
    public function setToWork($toWork)
    {
        $this->toWork = $toWork;

        return $this;
    }

    /**
     * Get toWork
     *
     * @return int
     */
    public function getToWork()
    {
        return $this->toWork;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return BaseMonth
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
     * Add holiday
     *
     * @param \UKMNorge\TidBundle\Entity\Holiday $holiday
     *
     * @return BaseMonth
     */
    public function addHoliday(\UKMNorge\TidBundle\Entity\Holiday $holiday)
    {
        $this->holidays[] = $holiday;

        return $this;
    }

    /**
     * Remove holiday
     *
     * @param \UKMNorge\TidBundle\Entity\Holiday $holiday
     */
    public function removeHoliday(\UKMNorge\TidBundle\Entity\Holiday $holiday)
    {
        $this->holidays->removeElement($holiday);
    }

    /**
     * Set holidayMinutes
     *
     * @param integer $holidayMinutes
     *
     * @return BaseMonth
     */
    public function setHolidayMinutes($holidayMinutes)
    {
        $this->holidayMinutes = $holidayMinutes;

        return $this;
    }

    /**
     * Get holidayMinutes
     *
     * @return integer
     */
    public function getHolidayMinutes()
    {
        return $this->holidayMinutes;
    }
}
