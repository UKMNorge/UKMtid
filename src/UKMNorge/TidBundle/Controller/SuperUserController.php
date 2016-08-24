<?php

namespace UKMNorge\TidBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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

        // TODO: Fjern, dette håndteres jo av firewallen
        if (!$uServ->isLoggedIn()) {
            throw $this->createAccessDeniedException('You cannot access this page!');
        }

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