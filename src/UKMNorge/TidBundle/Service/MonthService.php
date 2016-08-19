<?php

namespace UKMNorge\TidBundle\Service;

use Exception;
use DateTime;
use UKMNorge\TidBundle\Entity\User;
use UKMNorge\TidBundle\Entity\Month;
use UKMNorge\TidBundle\Entity\Interval;

# TODO: Restrukturer og gjør ting mer oversiktlig - veldig convoluted her, vil man ikke gjøre så mye som mulig på objektet??


class MonthService {
	public function __construct( $container ) {
		$this->container = $container;
		$this->doctrine = $container->get('doctrine');
		$this->em = $this->doctrine->getManager();
		$this->repo = $this->doctrine->getRepository("UKMTidBundle:Month");
	}

	public function get(User $user, $month, $year) {
		$m = $this->repo->getUserMonth($user, $month, $year);
		// Hvis brukeren ikke har noe data?
		if(null == $m)
			$m = $this->addMonth($user, $month, $year);

		return $m;
		#throw new Exception("Brukeren har ikke noe data registrert i denne måneden");
	}

	public function getAllIntervalsInMonth(User $user, $month, $year) {
		#$m = $this->repo->findBy(array('user'=> $user, 'month' => $month, 'year' => $year));
		$m = $this->get($user, $month, $year);
		return $m->getIntervals();

	}

	public function addToUserWorked(Interval $interval) {
		$iServ = $this->container->get('UKM.interval');
		$user = $iServ->getUserFromInterval($interval);
		$month = $iServ->getMonthFromInterval($interval);
		$year = $iServ->getYearFromInterval($interval);

		$m = $this->get($user, $month, $year);

		$minutes = $this->container->get('UKM.interval')->getMinutesWorkedFromInterval($interval);
		$minutes += $m->getWorked();
		$m->setWorked($minutes);

		$this->em->persist($m);
		$this->em->flush();

		return true;
	}

	// TODO: LOGIC
	public function getUserStatus(User $user, $month, $year, $format='minutes' ) {
		if( !$this->container->get('UKM.format')->isSupported('time', $format ) ) {
			throw new Exception('Unsupported format '. $format .' requested' );
		}
		
		$month = $this->get($user, $month, $year);
		$minutes = $month->getWorked() - $month->getToWork();
		return $this->container->get('UKM.format')->$format( $minutes );
	}

	// TODO: LOGIC
	public function getUserWorked(User $user, $month, $year, $format='minutes' ) {
		if( !$this->container->get('UKM.format')->isSupported('time', $format ) ) {
			throw new Exception('Unsupported format '. $format .' requested' );
		}
		
		$m = $this->get($user, $month, $year);
		
		return $this->container->get('UKM.format')->$format( $m->getWorked() );
	}

	// TODO: LOGIC
	# Skal returnere planlagt antall arbeidstimer.
	public function getToWork(User $user, $month, $year, $format='minutes' ) {
		if( !$this->container->get('UKM.format')->isSupported('time', $format ) ) {
			throw new Exception('Unsupported format '. $format .' requested' );
		}
		# Timing info
		if ($this->container->has('debug.stopwatch')) {
    		$stopwatch = $this->container->get('debug.stopwatch');
    		$stopwatch->start('MonthService->getToWork()');
		}

		$m = $this->get($user, $month, $year);
		$minutes = $m->getToWork();

		# TODO: Move this?
		# If we don't have the value, find it and cache it / store it on the object.
		if (null == $minutes) {
			$minutes = $this->findToWork($month, $year);	
			$m->setToWork($minutes);
			$em = $this->doctrine->getManager();
			$em->persist($m);
			$em->flush();
		}

		$minutes = $minutes * $user->getPercentage();
		$minutes = $minutes / 100;

		if ($this->container->has('debug.stopwatch')) {
    		$stopwatch = $this->container->get('debug.stopwatch');
    		$stopwatch->stop('MonthService->getToWork()');
		}
		#$minutes = ($this->findToWork($month, $year) * ($user->getPercentage()/100));
		return $this->container->get('UKM.format')->$format($minutes);
	}

	public function addNewInterval(User $user, $year, $month, $day, $hour, $minute) {
		$start = new DateTime();
		$start->setDate($year, $month, $day);
		$start->setTime($hour, $minute);

		$interval = $this->container->get('UKM.interval')->createNewInterval($user, $start);
		$this->addInterval($user, $month, $year, $interval);
		return $interval;
	}

	public function addInterval(User $user, $month, $year, Interval $interval) {
		$em = $this->doctrine->getManager();
		
		$m = $this->get($user, $month, $year);

		$interval->setMonth($m);
		
		$m->addInterval($interval);

		$em->persist($interval);
		$em->persist($m);
		$em->flush();
		return;
	}

	public function findDaysInMonth($month, $year) {
		return cal_days_in_month ( CAL_GREGORIAN, $month, $year);
	}

	private function addMonth(User $user, $month, $year) {
		$em = $this->doctrine->getManager();
		$toWork = $this->findToWork($month, $year);

		$m = new Month();
		$m->setUser($user)
			->setMonth($month)
			->setYear($year)
			->setWorked(0)
			->setToWork($toWork);
		
		$em->persist($m);
		$em->flush();
		return $m;
	}

	// TODO:
	private function findToWork($month, $year) {
		if ($this->container->has('debug.stopwatch')) {
    		$stopwatch = $this->container->get('debug.stopwatch');
    		$stopwatch->start('MonthService->findToWork()');
		}

		$workServ = $this->container->get('UKM.work');
		$minutes = $workServ->getTotalWorkMinutesForMonth($month, $year);
		#var_dump($minutes);
		#throw new Exception("MinutesToWork: ". $minutes);
		if ($this->container->has('debug.stopwatch')) {
    		$stopwatch = $this->container->get('debug.stopwatch');
    		$stopwatch->stop('MonthService->findToWork()');
		}
		return $minutes;
		#return 450;
	}

}