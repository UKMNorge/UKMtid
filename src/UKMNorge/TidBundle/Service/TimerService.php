<?php

namespace UKMNorge\TidBundle\Service;


/**
 * 
 * Quiet and simle way to get stopwatch entries on one line.
 * Inject service, and use like this: 
 * 	$timer->start('name'); 
 *  // some code
 *	$timer->stop('name');
 */
class TimerService {
	public function __construct($container) {
		$this->stopwatch = null;
		if ($container->has('debug.stopwatch')) 
			$this->stopwatch = $container->get('debug.stopwatch');
	}

	public function start($name = '') {
		// If no stopwatch, don't do anything.
		if (!$this->stopwatch)
			return;
		// No name supplied, use debug_backtrace to find calling function.
		if (!$name)
			$name = debug_backtrace(FALSE, 2)[1]['function'];
		
    	$this->stopwatch->start($name);
	}

	public function stop($name = '') {
		// If no stopwatch, don't do anything.
		if(!$this->stopwatch)
			return;
		// No name supplied, use debug_backtrace to find calling function.
		if (!$name)
			$name = debug_backtrace(FALSE, 2)[1]['function'];

    	$this->stopwatch->stop($name);
	}
}