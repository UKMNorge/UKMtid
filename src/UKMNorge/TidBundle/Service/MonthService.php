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
		$this->baseMonthService = $container->get('UKM.baseMonth');
		$this->logger = $this->container->get('logger');
	}

	public function get(User $user, $month, $year) {
		$bm = $this->baseMonthService->get($month, $year);
		$m = $this->repo->getUserMonth($user, $bm);
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
		
		#$m = $this->get($user, $month, $year);
		#$minutes = $m->getWorked() - $this->getMinutesToWork($user, $month, $year);

		$minutes = $this->getYearTotal($user, $month, $year);
		#dump($minutes);
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

	public function getToWork(User $user, $month, $year, $format ='minutes') {
		if( !$this->container->get('UKM.format')->isSupported('time', $format ) ) {
			throw new Exception('Unsupported format '. $format .' requested' );
		}
		$minutes = $this->getMinutesToWork($user, $month, $year);
		return $this->container->get('UKM.format')->$format($minutes);

	}

	public function getMinutesToWork(User $user, $month, $year) {
		$bm = $this->baseMonthService->get($month, $year);
		$minutes = $bm->getToWork() * ($user->getPercentage() / 100);
		if(false == $user->getExcludeHolidays())
			$minutes = $minutes - $bm->getHolidayMinutes();

		return $minutes;
	}

	private function getUserWorkedTotal(User $user, $currentMonth, $year) {
		return $this->repo->getYearTotal($user, $currentMonth, $year);
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

		$bm = $this->baseMonthService->get($month, $year);

		$m = new Month();
		$m->setUser($user)
			->setMonth($bm)
			#->setYear($year)
			->setWorked(0);
		
		$em->persist($m);
		$em->flush();
		return $m;
	}



	public function getYearTotal(User $user, $currentMonth, $year) {
		list($toWork, $holidayMinutes) = $this->doctrine
			->getRepository("UKMTidBundle:BaseMonth")
			->getYearTotal($currentMonth, $year);
		#dump($res);
		#$toWork = $res[0][1];
		#$holidayMinutes = $res[0][2];
		#$toWork = $res[''];
		#list($toWork, $holidayMinutes);
		$userWorked = $this->getUserWorkedTotal($user, $currentMonth, $year);
		#dump($userWorked);
		#dump($toWork);
		#dump($holidayMinutes);
		$userToWork = $toWork * ($user->getPercentage() / 100);
		#dump($userToWork);
		$minutes = $userWorked - $userToWork;
		#dump($minutes);
		if($user->getExcludeHolidays())
			return $minutes;
		return $minutes + $holidayMinutes;
	}
	

}