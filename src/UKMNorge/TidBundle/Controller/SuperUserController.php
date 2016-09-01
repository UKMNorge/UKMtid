<?php

namespace UKMNorge\TidBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Exception;

class SuperUserController extends Controller
{
    public function disabledAction()
    {
        return $this->render('UKMTidBundle:SuperUser:disabled.html.twig');
    }

    public function adminAction() {

        $data = array();
        $data['uServ'] = $this->get('UKM.user');
        $data['dServ'] = $this->get('UKM.department');
        return $this->render('UKMTidBundle:SuperUser:admin.html.twig', $data);
    }

    public function newDepartmentAction(Request $request) {
        $dServ = $this->get('UKM.department');
        $uServ = $this->get('UKM.user');

        $name = $request->request->get('name');
        $dServ->addDepartment($name);

        $this->addFlash('success', "Kontoret '".$name."' ble lagt til.");
        return $this->redirectToRoute('ukm_tid_superuser_admin');
    }

    public function addToDepartmentAction(Request $request) {
    	$dServ = $this->get('UKM.department');
    	$uServ = $this->get('UKM.user');

        $dep_id = $request->request->get('dep_id');
        $user_id = $request->request->get('user_id');

    	$dep = $dServ->get($dep_id);
    	$user = $uServ->get($user_id);
    	$dServ->addMember($dep, $user);

    	$this->addFlash('success', "Bruker ".$user->getName()." ble lagt til i gruppen ".$dep->getName().".");
    	return $this->redirectToRoute('ukm_tid_superuser_admin');
    }

    public function enableAction(Request $request) {
        $user_id = $request->request->get('user_id');
        $percentage = $request->request->get('percentage');

    }  

    // TODO: Not implemented properly - needed for dynamic holidays etc.
    public function rekalkulerAction(Request $request) {
        $bmServ = $this->get('UKM.baseMonth');
        $bm = $bmServ->rekalkulerMinutter(8, 2016);


        throw new Exception("Stop.");
    }

    public function excludeHolidaysAction(Request $request) {
        $value = $request->request->get('excludeHolidays');
        $uServ = $this->get('UKM.user');
        $user = $uServ->get($request->request->get('user_id'));


        try {
            $res = $uServ->setExcludeHolidays($user, (bool)$value);
            if($res)
                $this->addFlash('success', 'Lagret endringer');
        } catch(Exception $e) {
            $this->addFlash('danger', "Klarte ikke å endre verdien på excludeHolidays til ".$value." for bruker ".$user->getName().".");
        }
        
        return $this->redirectToRoute('ukm_tid_superuser_admin');
    }

    public function setPercentageAction(Request $request) {
        $user_id = $request->request->get('user_id');
        $percentage = $request->request->get('percentage');

        $uServ = $this->get('UKM.user');
        $user = $uServ->get($user_id);
        $res = $uServ->setPercentage($user, $percentage);

        if($res)
            $this->addFlash('success', 'Bruker '.$user->getName().' har nå stillingsprosent '.$percentage);
        else
            $this->addFlash('danger', 'Kunne ikke sette stillingsprosent \''.$percentage.'\' på bruker '.$user->getName().'.'); 

        return $this->redirectToRoute('ukm_tid_superuser_admin');
    }
}