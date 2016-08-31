<?php

namespace UKMNorge\TidBundle\Service;

use Exception;
use DateTime;
use DateTimeZone;
use UKMNorge\TidBundle\Entity\Interval;
use UKMNorge\TidBundle\Entity\User;

class IntervalService {
	private $doctrine;
	private $repo;

	public function __construct($container) {
		$this->timer = $container->get('UKM.timer');
		$this->timer->start('IntervalService::__construct()');
		
		$this->container = $container;
		$this->doctrine = $container->get('doctrine');
		$this->repo = $this->doctrine->getRepository("UKMTidBundle:Interval");
		$this->em = $this->doctrine->getManager();
		$this->mServ = $container->get('UKM.month');
		$this->formatter = $container->get('UKM.format');

		$this->timer->stop('IntervalService::__construct()');
	}

	// Hent det nyeste intervallet som ikke er stoppet.
	public function getCurrentInterval(User $user) {
		#throw new Exception("STOP");
		return $this->repo->findOneBy(array("userid" => $user->getId(), "stop" => null), array('start' => 'DESC'));
	}

	public function get($id) {
		return $this->repo->findOneBy(array('id' => $id));
	}

	public function createNewInterval(User $user, $start) {
		if (null == $start) 
			$interval = new Interval($user->getId());
		else	
			$interval = new Interval($user->getId(), $start->getTimestamp());

		// TODO: Persist the interval or don't bother?

		return $interval;
	}

	public function isStopped(Interval $interval) {
		$stop = $interval->getStop();
		if (null != $stop) {
			return true;
		}
		return false;
	}

	public function getLengthInMinutes(Interval $interval) {
		return $interval->getLengthInMinutes();
	}

	public function getPrettyLength(Interval $interval) {
		$length = $this->getLengthInMinutes($interval);
		$string = '';
		if( $length > 60 ) {
			$hours = $this->formatter->hours($length, 0, true);	
			$string = $hours;
		}
		if ($length % 60) {
			$minutes = $this->formatter->minutes($length % 60, true);
			$string = $string . ' ' . $minutes;
		}

		return $string;
	}

	public function stopInterval(Interval $interval, $year, $month, $day, $hour, $minute) {
		$stop = new DateTime();
    	$stop->setDate($year, $month, $day);
    	$stop->setTime($hour, $minute);

		$interval->setStopDateTime($stop);

		// Save changes?
		$this->em->persist($interval);
		$this->em->flush();
		// Update month with new data
		$month = $this->mServ->addToUserWorked($interval);
		return;
	}

	public function getMinutesWorkedFromInterval(Interval $interval) {
		$seconds = $interval->getStop() - $interval->getStart();
		if ( $seconds < 0 ) {
			#dump($interval);
			#dump($seconds);
			throw new Exception("Intervallet er negativt, og vi kan derfor ikke beregne arbeidstid fra det.");
		}
		# TODO: Støtt intervaller som er lengre enn dette?
		elseif( $seconds > 86400) 
			throw new Exception("Intervallet varer lengre enn 24 timer, kan dette stemme?");
		return (integer)$seconds/60;
	}
	
	public function getStart(Interval $interval) {
		return $interval->getStart();
	}

	public function getStop(Interval $interval) {
		return $interval->getStop();
	}

	public function getUserFromInterval(Interval $interval) {
		return $interval->getMonth()->getUser();
	}

	public function getStartMinuteFromInterval(Interval $interval) {
		return date("i", $interval->getStart());
	}

	public function getHourFromInterval(Interval $interval) {
		return date("H", $interval->getStart());
	}

	# Returnerer dagen intervallet ble startet.
	public function getDayFromInterval(Interval $interval) {
		return date("j", $interval->getStart());
	}

	// TODO: Fiks fornorsking av dag
	public function getDayNameFromInterval(Interval $interval) {
		return date("D", $interval->getStart());
	}

	# Returnerer måneden intervallet ble startet.
	public function getMonthFromInterval(Interval $interval) {
		return date("n", $interval->getStart());
	}

	public function getYearFromInterval(Interval $interval) {
		return date("Y", $interval->getStart());
	}

	### GET STOP-stuff
	public function getStopMinuteFromInterval(Interval $interval) {
		return date("i", $interval->getStop());
	}

	public function getStopHourFromInterval(Interval $interval) {
		return date("H", $interval->getStop());
	}

	public function getStopDayFromInterval(Interval $interval) {
		return date("j", $interval->getStop());
	}

	// TODO: Fiks fornorsking av dag
	public function getStopDayNameFromInterval(Interval $interval) {
		return date("D", $interval->getStop());
	}

	public function getStopMonthFromInterval(Interval $interval) {
		return date("n", $interval->getStop());
	}

	public function getStopYearFromInterval(Interval $interval) {
		return date("Y", $interval->getStop());
	}

	### GET CURRENT-stuff
	public function getCurrentDateTime() {
		return new DateTime("now", new DateTimeZone("Europe/Oslo"));
	}
	public function getCurrentMinute() {
		return $this->getCurrentDateTime()->format("i");
		#return date("i");
	}

	public function getCurrentHour() {
		// Oops, make sure this is for correct timezone!
		return $this->getCurrentDateTime()->format("H");
		#return date("H");
	}

	public function getCurrentDay() {
		return $this->getCurrentDateTime()->format("j");
		#return date("j");
	}

	public function getCurrentMonth() {
		return $this->getCurrentDateTime()->format("n");
		#return date("n");
	}

	public function getCurrentYear() {
		return $this->getCurrentDateTime()->format("Y");
		#return date("Y");
	}

	public function getMonths() {
		$months = array();
		$months[1] = 'Januar';
		$months[2] = 'Februar';
		$months[3] = 'Mars';
		$months[4] = 'April';
		$months[5] = 'Mai';
		$months[6] = 'Juni';
		$months[7] = 'Juli';
		$months[8] = 'August';
		$months[9] = 'September';
		$months[10] = 'Oktober';
		$months[11] = 'November';
		$months[12] = 'Desember';
		return $months;
	}

	// TODO: Sett år som skal være tilgjengelig.
	public function getAvailableYears() {
		$years = array();
		$years[] = 2016;
		return $years;
	}

}