<?php

namespace UKMNorge\TidBundle\Service;

use Exception;


class FormatService {
	var $supported = null;
	
	public function __construct() {
		$this->supported['time'] = array('minutes', 'hours', 'days', 'weeks');
	}
	// TODO: LOGIC
	private function _time( $minutes, $format='min' ) {
		return new format( $minutes, $format );
	}
	
	
	public function minutes( $minutes, $short = false  ) {
		return $this->_time( $minutes, $short ? 'm' : 'minutter');
	}
	
	public function hours( $minutes, $precision = 1, $short = false ) {
		return $this->_time( round($minutes/60, $precision), $short ? 't' : 'timer');
	}
	
	public function days( $minutes, $precision = 1, $short = false ) {
		return $this->_time( round($minutes/(60*7.5),$precision), $short ? 'd' : 'dager');
	}
	
	public function weeks( $minutes ) {
		return $this->_time( round($minutes/(60*37.5),1), 'uker');
	}

	public function isSupported( $what, $format ) {
		return in_array($format, $this->supported[$what] );
	}
}

class format {
	public function __construct( $time, $format ) {
		$this->time = $time;
		$this->format = $format;
	}
	
	public function __toString() {
		return $this->time .' '. $this->format;
	}
	public function getValue() {
		return $this->time;
	}
	public function getFormat() {
		return $this->format;
	}
}