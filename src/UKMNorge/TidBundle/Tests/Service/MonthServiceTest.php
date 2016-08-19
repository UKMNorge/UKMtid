<?php

namespace UKMNorge\TidBundle\Tests\Service;

use UKMNorge\TidBundle\MonthService;

class MonthServiceTest extends \PHPUnit_Framework_TestCase
{
	# Tester FindToWork-metoden med en måned som er regnet ut manuelt
	# August 2016 har 24 arbeidsdager, ingen helligdager.
	# Det blir 24*7.5*60 = 10800 minutter
    public function testFindToWork()
    {
    	$monthServ = new MonthService();
    	$minutesToWork = $monthServ->findToWork(8, 2016);
    	
        $this->assertEquals($minutesToWork, 10800);
    }
}
