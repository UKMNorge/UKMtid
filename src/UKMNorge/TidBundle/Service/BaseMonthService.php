<?php

namespace UKMNorge\TidBundle\Service;

use UKMNorge\TidBundle\Entity\BaseMonth;

use Exception;

class BaseMonthService {

	public function __construct($doctrine, $timer, $workService, $holidayService) {
		$this->doctrine = $doctrine;
		$this->timer = $timer;
		$this->workService = $workService;
		$this->holidayService = $holidayService;

	}

	public function get($month, $year) {
		$bm = $this->doctrine->getRepository("UKMTidBundle:BaseMonth")->findOneBy(array('month' => $month, 'year' => $year));
		
		if(!$bm) {
			$bm = $this->create($month, $year);			
		}

		return $bm;
	}

	// Oppretter en ny basemåned
	private function create($month, $year) {
		$bm = new BaseMonth();
		$bm->setMonth($month)
			->setYear($year)
			->setName($this->monthToNiceName($month))
			->setWeekdays($this->workService->getWeekdaysForMonth($month, $year));
		
		dump($bm);
		$this->doctrine->getManager()->persist($bm);
		$this->rekalkulerMinutter($month, $year, $bm);

		$this->doctrine->getManager()->flush();
	}

	public function rekalkulerMinutter($month, $year, $bm = null) {
		if(!$bm)
			$bm = $this->get($month, $year);
		$bm->setHolidayMinutes($this->getHolidayMinutesForMonth($month, $year, $bm));
	
		// Find toWork based on current data.
		$toWork = $this->findToWork($bm, $month, $year);
		if(null == $toWork) {
			throw new Exception("findToWork returnerte null, og ikke en gitt mengde minutter.");
		}
		$bm->setToWork($toWork);

		$this->doctrine->getManager()->persist($bm);
		$this->doctrine->getManager()->flush();
		return $bm;	
	}

	// TODO: Finn faktiske helligdager (i WorkService)
	private function getHolidayMinutesForMonth($month, $year, $bm) {
		$this->holidayService->load_holidays($month, $year, $bm);
		dump($bm);
		$holidays = $this->doctrine->getRepository("UKMTidBundle:Holiday")->findBy(array('month' => $bm));
		dump($holidays);
		if(null == $holidays) {
			return 0;
		}
		$minutes = 0;
		foreach ($holidays as $day) {
			$minutes = $minutes + $day->getTimeOff();
		}
		dump($minutes);
		return $minutes;
	}

	// TODO:
	private function findToWork($bm, $month, $year) {
		#$this->timer->start('findToWork()');

 		$weekMinutes = $bm->getWeekdays() * 450; # TODO: Move minutes per day (450) to a setting.

		#$minutes = $this->workService->getTotalWorkMinutesForMonth($month, $year);
		#var_dump($minutes);
		#throw new Exception("MinutesToWork: ". $minutes);
		#$this->timer->stop('findToWork()');

		return $weekMinutes;
		#return 450;
	}

	# Skal returnere planlagt antall arbeidsminutter.
	public function getToWork($month, $year ) {
		$bm = $this->get($month, $year);
		$minutes = $bm->getToWork();
		if( null == $minutes) {
			throw new Exception("Måneden har ikke fått lagret antall arbeidsminutter.");
		}
		return $minutes;
	}

	public function monthToNiceName($month) {
		switch ($month) {
			case 1:
				return 'januar';
				break;
			case 2:
				return 'februar';
				break;
			case 3:
				return 'mars';
				break;
			case 4:
				return 'april';
				break;
			case 5:
				return 'mai';
				break;
			case 6:
				return 'juni';
				break;
			case 7:
				return 'juli';
				break;
			case 8:
				return 'august';
				break;
			case 9:
				return 'september';
				break;
			case 10:
				return 'oktober';
				break;
			case 11:
				return 'november';
				break;
			case 12:
				return 'desember';
				break;
			default:
				return false;
				break;
		}
	}
}