<?php

namespace UKMNorge\TidBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\ManyToOne;
use AppBundle\Logger;
use \DateTime as DateTime;
use Exception;

/**
 * Interval
 *
 * @ORM\Table(name="`interval`")
 * @ORM\Entity(repositoryClass="UKMNorge\TidBundle\Repository\IntervalRepository")
 */
class Interval
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
     * @ORM\Column(name="start", type="integer")
     */
    private $start;

    /**
     * @var int
     *
     * @ORM\Column(name="stop", type="integer", nullable=true)
     */
    private $stop;

    /**
     *
     * 
     * @ORM\Column(name="userid", type="integer", nullable=false)
     */
    private $userid;


    /**
     * @ORM\ManyToOne(targetEntity="Month", inversedBy="intervals")
     */
    private $month;

    /**
     * All intervals must be related to a user and have a start-time or be started right now.
     * 
     */
    public function __construct($userid, $start = null) {

        $this->userid = $userid;
        if ($start != null )
            $this->start = $start;
        else
            $this->start();
            

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
     * Start a new interval by setting the current time as the start-point.
     *
     * @return Interval
     */
    public function start() {
        $date = new DateTime;
        $this->start = $date->getTimestamp();
        return $this;
    }

    /**
     * Set start
     *
     * @param integer $start
     *
     * @return Interval
     */
    public function setStart($start)
    {
        $this->start = $start;
        return $this;
    }

    /**
     * Set start-time from a DateTime-object.
     *
     * @param DateTime $start
     *
     * @return Interval
     */
    public function setStartDateTime(DateTime $start)
    {
        $this->start = $start->getTimestamp();
        return $this;
    }

    /**
     * Get start
     *
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get starttime as a DateTime object
     *
     * @return DateTime
     */
    public function getStartDateTime()
    {
        $date = new DateTime();
        $date->setTimestamp($this->start);
        return $date;
        #return $this->start;
    }

    /**
     * Set stop
     *
     * @param integer $stop
     *
     * @return Interval
     */
    public function setStop($stop)
    {
        $this->stop = $stop;

        return $this;
    }

    /**
     * Set stop-time from a DateTime-object.
     *
     * @param DateTime $stop
     *
     * @return Interval
     */
    public function setStopDateTime(DateTime $stop)
    {
        $this->stop = $stop->getTimestamp();
        return $this;
    }

    /**
     * Get stop
     *
     * @return int
     */
    public function getStop()
    {
        return $this->stop;
    }

    /**
     * Get stoptime as a DateTime-object
     *
     * @return DateTime
     */
    public function getStopDateTime()
    {
        $date = new DateTime();
        $date->setTimestamp($this->stop);
        return $date;
    }

    /**
     * Set userid
     *
     * @param integer $userid
     *
     * @return Interval
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return integer
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Get user.
     *
     * @return integer
     */
    public function getUser()
    {
        return new User($this->userid);
    }

    /**
     * Set month
     *
     * @param \UKMNorge\TidBundle\Entity\Month $month
     *
     * @return Interval
     */
    public function setMonth(\UKMNorge\TidBundle\Entity\Month $month = null)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return \UKMNorge\TidBundle\Entity\Month
     */
    public function getMonth()
    {
        return $this->month;
    }

    public function getLengthInSeconds() 
    {
        return $this->getStop() - $this->getStart();
    }
    public function getLengthInMinutes()
    {
        return (int) ($this->getLengthInSeconds() / 60);
    }
}
