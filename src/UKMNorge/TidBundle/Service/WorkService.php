<?php

namespace UKMNorge\TidBundle\Service;

use UKMNorge\TidBundle\Entity\Holiday;
use UKMNorge\TidBundle\Entity\Option;
use DateTime;
use Exception;

require_once('UKM/curl.class.php');

use UKMCURL;

class WorkService {

	private $options;
	private $doctrine;

	public function __construct($option, $doctrine) {
		$this->options = $option;
		$this->doctrine = $doctrine;
	}

	public function getTotalWorkMinutesForMonth($month, $year) {
		$holidayMinutes = $this->getHolidayMinutesForMonth($month, $year);
		$weekdayMinutes = $this->getWeekdayMinutesForMonth($month, $year);
		
		return $weekdayMinutes - $holidayMinutes;
	}

	public function getHolidayMinutesForMonth($month, $year) {
		$repo = $this->doctrine->getRepository("UKMTidBundle:Holiday");
		$holidaysThisMonth = $repo->getHolidays($year, $month);

		if ( null == $holidaysThisMonth ) {
			return 0;
		}	

		// Here we have holidays loaded.
		$minutes = 0;
		foreach($holidaysThisMonth as $holiday) {
			$minutes += $holiday->getTimeOff();
		}

		return $minutes;
	}

	public function getWeekdayMinutesForMonth($month, $year ) {
		$curl = new UKMCURL();
		$result = $curl->process('https://webapi.no/api/v1/calendar/2016');

		$minutes = 0;
		
		# For alle dager i gitt måned
		foreach ($result->data->months[$month-1]->days as $day) {
			# Hvis dagen er helg, hopp over.
			if ($day->name == "Saturday" || $day->name == "Sunday") {
				continue;
			}
			# Ellers, tell minuttene herfra. 
			else {
				$minutes += 450; # Full arbeidsdag i minutt; 7.5*60
			}
		}
		
		return $minutes;
	}

	public function getWeekdaysForMonth($month, $year) {
		return $this->workdays_in_month($year, $month);
	}

	public function workdays_in_month($year, $month) {
		$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$first_day = (int) date('N', mktime(0,0,0, $month, 1, $year ) );
		$last_day = (int) date('N', mktime(0,0,0, $month, $days_in_month, $year));

		// THE FIRST WEEK
		$workdays_in_first_week = ($first_day == 7) ? 0 : (5 - ($first_day-1));
		// Substract days in first week to get $remaining_days_in_month (used to calc mid-weeks and last week)
		$remaining_days_in_month = $days_in_month - abs(7 - ($first_day-1));
		// THE MID WEEKS
		$full_weeks = floor($remaining_days_in_month / 7);
		// THE LAST WEEK
		$workdays_in_last_week = $remaining_days_in_month - (7*$full_weeks) - ($last_day > 5 ? (7-$last_day) : 0);

		return $workdays_in_first_week + ($full_weeks*5) + $workdays_in_last_week;
	}

	
}