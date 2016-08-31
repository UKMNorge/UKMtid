<?php

namespace UKMNorge\TidBundle\Service;

require_once('UKM/curl.class.php');

use UKMNorge\TidBundle\Entity\Holiday;
use DateTime;
use UKMCURL;


class HolidayService {
	
	public function __construct($doctrine, $timer) {
		$this->timer = $timer;
		$this->doctrine = $doctrine;
	}

	public function load_holidays( $month, $year, $basemonth ) {
		$curl = new UKMCURL();
		#$url = $this->option->get('holiday_url');
		$result = $curl->process('https://webapi.no/api/v1/holydays/2016');

		$holidays = array();
		$em = $this->doctrine->getManager();
		$repo = $this->doctrine->getRepository("UKMTidBundle:Holiday");

		foreach ($result->data as $holiday) {
			$dato = new DateTime($holiday->date);

			// Er dette måneden vi bryr oss om?
			if($dato->format('n') < $month) {
				continue;
			} elseif($dato->format('n') > $month) {
				break;
			}

			# Sjekk om helligdagen finnes i databasen fra før
			if ($repo->findOneBy(array("date" => $dato))) {
				# TODO: Mulig slett eller oppdater data her
				continue;
			}

			# Hvis ukedagen er helg (0 = søndag, 6 = lørdag), hopp over.
			if (0 < $dato->format("w") && $dato->format("w") < 6) {
				$h = new Holiday();
				$h->setDate($dato);
				$h->setMonth($basemonth);
				$h->setYear($dato->format("Y"));
				$h->setName($holiday->description);

				$em->persist($h);
			}
		}

		$em->flush();
		## WHEN DONE
		#$this->options->set('holiday_loaded_'.$year, true);

	}	
}