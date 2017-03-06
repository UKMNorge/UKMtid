<?php

namespace UKMNorge\TidBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HTTPFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Exception;
use DateTime;
use UKMNorge\TidBundle\Entity\Interval;

class UserController extends Controller
{	
    public function registerAction()
    {	
    	$userServ = $this->get('UKM.user');
    	$iServ = $this->get('UKM.interval');
    	$mServ = $this->get('UKM.month');
        $logger = $this->get('logger');

        $timer = $this->get('UKM.timer');
        $timer->start('isLoggedIn');

    	if(!$userServ->isLoggedIn()) {
            $logger->info('UKMTidBundle: Ikke logget inn, sender brukeren videre til innlogging.');
            return $this->redirectToRoute('ukm_tid_validering');
            #return $this->redirectToRoute('ukm_dip_login');
    		throw new Exception("Not implemented - send to Delta when not logged in. Do this from UserService, maybe?");
    		return;
    	}

        if(!$userServ->isValid()) {
            return $this->redirectToRoute('ukm_tid_validering');
        }

        $timer->stop('isLoggedIn');

    	$user = $userServ->getCurrent();
    	$interval = $iServ->getCurrentInterval($user);

		$data = array();    	
    	$data['iServ'] = $iServ;
    	$data['mServ'] = $mServ;
    	if(null == $interval) {
    		## Return start-view
    		return $this->render('UKMTidBundle:User:start.html.twig', $data);	
    	}
    	## Return stop-view    	
    	$data['interval'] = $interval;
    	return $this->render('UKMTidBundle:User:stop.html.twig', $data);
    }

    /**
     * @Route("/user/stop/", name="ukm_tid_stop", requirements={"interval_id": "\d+", "day": "\d+", "month": "\d+", "hour": "\d+", "minute": "\d+"})
     * @Method({"POST"})
     */
    public function stopAction(Request $request) {

    	#dump($request);
    	#die();
    	$user = $this->get('UKM.user')->getCurrent();
    	$iServ = $this->get('UKM.interval');
    	$repo = $this->get('doctrine')->getRepository('UKMTidBundle:Interval');
    	$params = $request->request;

    	$interval = $repo->findOneBy(array('id' => $params->get('interval_id')));
    	$year = $params->get('stopYear');
    	$month = $params->get('stopMonth');
    	$day = $params->get('stopDay');
    	$hour = $params->get('stopHour');
    	$minute = $params->get('stopMin');

    	#dump($interval);
    	$iServ->stopInterval($interval, $year, $month, $day, $hour, $minute);
    	#dump($interval);
    	$data = array();
    	$data['id'] = $user->getId();
    	$data['year'] = $year;
    	$data['month'] = $month;
    	return $this->redirectToRoute('ukm_tid_user', $data);
    }

    /**
     * @Route("/user/start/", name="ukm_tid_start", requirements={"day": "\d+", "month": "\d+", "hour": "\d+", "minute": "\d+"})
     * @Method({"POST"})
     */
    public function startAction(Request $request) {

    	#dump($request);
    	#die();
    	$user = $this->get('UKM.user')->getCurrent();
    	$iServ = $this->get('UKM.interval');
    	$mServ = $this->get('UKM.month');
    	$repo = $this->get('doctrine')->getRepository('UKMTidBundle:Interval');
    	$params = $request->request;

    	$interval = $repo->findOneBy(array('id' => $params->get('interval_id')));
    	$year = $params->get('year');
    	$month = $params->get('month');
    	$day = $params->get('day');
    	$hour = $params->get('hour');
    	$minute = $params->get('minute');

    	#dump($interval);
    	$interval = $mServ->addNewInterval($user, $year, $month, $day, $hour, $minute);
    	#$iServ->stopInterval($interval, $year, $month, $day, $hour, $minute);

    	#dump($interval);
    	$data = array();
    	$data['id'] = $user->getId();
    	$data['year'] = $year;
    	$data['month'] = $month;
    	return $this->redirectToRoute('ukm_tid_user', $data);
    }
    /**
     * @Route("/user-{id}/{year}-{month}/", name="ukm_tid_user")
     */
    public function reportAction($id, $year, $month) {
	    $userServ = $this->get('UKM.user');
	    $monthServ = $this->get('UKM.month');
	    $iServ = $this->get('UKM.interval');
	    $user = $userServ->get($id);
	    
		$month = ltrim($month, "0");
	    $status = $monthServ->getUserStatus($user, $month, $year);
	    #var_dump($status);
	    

	   # $interval = new Interval($id);
	    
	    #$m = $monthServ->get($user, $month, $year);
	    #$interval->setMonth($m);
	    #dump($interval);
	    
		# Timing info
		if ($this->has('debug.stopwatch')) {
    		$stopwatch = $this->get('debug.stopwatch');
    		$stopwatch->start('workdays_in_month');
		}

		$workServ = $this->get('UKM.work');
		$days = $workServ->workdays_in_month(2016, 1);
		# Timing info
		if ($this->has('debug.stopwatch')) {
    		$stopwatch = $this->get('debug.stopwatch');
    		$stopwatch->stop('workdays_in_month');
		}

	   # dump($iServ->getCurrentInterval($user));
	    #$monthServ->addInterval($user, $month, $year, $interval);
	    #$workServ = $this->get('UKM.work');
	    #$wm = $workServ->getWeekdayMinutesForMonth($month, $year);
	    

	    #throw new Exception("Stop");
		$data = array();
		$data['mServ'] = $monthServ;
        $data['bmServ'] = $this->get('UKM.baseMonth');
		$data['iServ'] = $iServ;
		$data['format'] = $this->get('UKM.format');
		$data['selected_user'] = $user;
		$data['selected_month'] = $month;
		$data['selected_year'] = $year;
	    return $this->render('UKMTidBundle:User:month.html.twig', $data);
    }

    public function editAction(Request $request) {
        $iServ = $this->get('UKM.interval');
        $mServ = $this->get('UKM.month');

        #$interval = $iServ->get($id);
        $id = $request->get('id');
        #dump($id);
        $interval = $iServ->get($id);

        if(!is_object($interval)) {
            throw new Exception('UKMTidBundle: Klarte ikke å hente intervallet du spør om!');
        }
        $month = $iServ->getMonthFromInterval($interval);
        $year = $iServ->getYearFromInterval($interval);
        $data = array();

        $data['editing'] = true;
        $data['interval'] = $interval;

        $data['today'] = $iServ->getDayFromInterval($interval);
        $data['days'] = $mServ->findDaysInMonth($month, $year);
        $data['thisMonth'] = $month;
        $data['months'] = $iServ->getMonths();
        $data['thisYear'] = $year;
        $data['years'] = $iServ->getAvailableYears();
        $data['thisHour'] = $iServ->getHourFromInterval($interval);
        $data['thisMinute'] = $iServ->getStartMinuteFromInterval($interval);

        $data['stopDay'] = $iServ->getStopDayFromInterval($interval);
        $data['stopDays'] = $mServ->findDaysInMonth($month, $year);
        $data['stopMonth'] = $month;
        $data['stopYear'] = $year;
        $data['stopHour'] = $iServ->getStopHourFromInterval($interval);
        $data['stopMinute'] = $iServ->getStopMinuteFromInterval($interval);
	    var_dump( $data['stopDay'] ); var_dump( $data );
	    if( $data['stopDay'] == null ) {
		$data['stopDay'] = date('d');
		$data['stopMonth'] = date('m');
		$data['stopHour'] = date('H');
		$data['stopMinute'] = date('i');
	    }
        return $this->render('UKMTidBundle:User:edit.html.twig', $data);
    }

    public function saveEditAction(Request $request) {
        $iServ = $this->get('UKM.interval');

        $params = $request->request;
        $interval = $this->getDoctrine()->getRepository("UKMTidBundle:Interval")->findOneBy(array('id' => $params->get('interval_id')));
        
        ## Start-tid:
        $year = $params->get('year');
        $month = $params->get('month');
        $day = $params->get('day');
        $hour = $params->get('hour');
        $minute = $params->get('minute');

        $start = new DateTime();
        $start->setDate($year, $month, $day);
        $start->setTime($hour, $minute);

        ## Stopp-tid:
        $year = $params->get('stopYear');
        $month = $params->get('stopMonth');
        $day = $params->get('stopDay');
        $hour = $params->get('stopHour');
        $minute = $params->get('stopMin');
        
        $stop = new DateTime();
        $stop->setDate($year, $month, $day);
        $stop->setTime($hour, $minute);

        // Do actual save:
        $iServ->updateInterval($interval, $start, $stop);

        // Finish up - redirect to overview
        $data = array();
        $data['id'] = $this->get('UKM.user')->getCurrent()->getId();
        $data['month'] = $month;
        $data['year'] = $year;
        return $this->redirectToRoute('ukm_tid_user', $data);
    }
        
    public function validateInfoAction(Request $request) {

        // Hvis brukeren både er valid og logget inn, redirect til framsiden?
        
        $data = array();
        $data['uServ'] = $this->get('UKM.user');
        $data['isLoggedIn'] = $this->get('UKM.user')->isLoggedIn();
        return $this->render('UKMTidBundle:User:login.html.twig', $data);
    }

	/**
	 * _throwJSONException
	 *
	 * Render an exception as JSON-data ready for mMessage-render in GUI
	 * 
	 * @param \Exception $e
	 * @return JSONResponse
	**/	
	private function _throwJSONException( $e ) {
		return new JsonResponse( array('success'=>false, 'errorMessage'=>$e->getMessage(), 'errorCode'=>$e->getCode() ) );
	}
}
