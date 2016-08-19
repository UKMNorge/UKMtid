<?php

namespace UKMNorge\TidBundle\Tests\Service;

use UKMNorge\TidBundle\WorkService;

class WorkServiceTest extends \PHPUnit_Framework_TestCase
{
	# Tester FindToWork-metoden med en måned som er regnet ut manuelt
	# August 2016 har 23 arbeidsdager, ingen helligdager.
	# Det blir 23*7.5*60 = 10350 minutter
    public function testGetTotalWorkMinutesForMonth()
    {
    	$workServ = new WorkService();
    	$minutesToWork = $workServ->getTotalWorkMinutesForMonth(8, 2016);
    	
        $this->assertEquals($minutesToWork, 10350);
    }

    public function testGetWeekdayMinutesForMonth() {
        $workServ = new WorkService();
        $minutesToWork = $workServ->getWeekdayMinutesForMonth(8,2016);

        $this->assertEquals($minutesToWork, 10350);
    }
}
