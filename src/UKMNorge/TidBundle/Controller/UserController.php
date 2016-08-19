<?php

namespace UKMNorge\TidBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HTTPFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Exception;
use UKMNorge\TidBundle\Entity\Interval;

class UserController extends Controller
{	
	/**
	 * @Route("/", name="ukm_tid_register")
	 */
    public function registerAction()
    {	
    	$userServ = $this->get('UKM.user');
    	$iServ = $this->get('UKM.interval');
    	$mServ = $this->get('UKM.month');

    	if(!$userServ->isLoggedIn()) {
    		throw new Exception("Not implemented - send to Delta when not logged in. Do this from UserService, maybe?");
    		return;
    	}

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
     * @Route("/stop/", name="ukm_tid_stop", requirements={"interval_id": "\d+", "day": "\d+", "month": "\d+", "hour": "\d+", "minute": "\d+"})
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
    	$year = $params->get('year');
    	$month = $params->get('month');
    	$day = $params->get('day');
    	$hour = $params->get('hour');
    	$minute = $params->get('minute');

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
     * @Route("/start/", name="ukm_tid_start", requirements={"day": "\d+", "month": "\d+", "hour": "\d+", "minute": "\d+"})
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
		$data['iServ'] = $iServ;
		$data['format'] = $this->get('UKM.format');
		$data['selected_user'] = $user;
		$data['selected_month'] = $month;
		$data['selected_year'] = $year;
	    return $this->render('UKMTidBundle:User:month.html.twig', $data);
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
