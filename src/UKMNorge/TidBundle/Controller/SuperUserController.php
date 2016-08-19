<?php

namespace UKMNorge\TidBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class SuperUserController extends Controller
{
    public function disabledAction()
    {
        return $this->render('UKMTidBundle:SuperUser:disabled.html.twig');
    }

    /**
     *
     * @Route("/superuser/new/{name}", name="ukmtid_department_new") 
     */
    public function newDepartmentAction($name) {
        $dServ = $this->get('UKM.department');
        $uServ = $this->get('UKM.user');

        // TODO: Fjern, dette håndteres jo av firewallen
        if (!$uServ->isLoggedIn()) {
            throw $this->createAccessDeniedException('You cannot access this page!');
        }

        $dServ->addDepartment($name);

        dump($dServ->getDepartments());
        die();
    }


    /**
     * @Route("/superuser/addToDepartment/{dep_id}-{user_id}", name="ukmtid_users_addToDepartment")
     */
    public function addToDepartmentAction($dep_id, $user_id) {
    	$dServ = $this->get('UKM.department');
    	$uServ = $this->get('UKM.user');

    	$dep = $dServ->get($dep_id);
    	$user = $uServ->get($user_id);
    	dump($dep);
    	dump($user);
    	$dServ->addMember($dep, $user);

    	dump($dep);
    	dump($dServ->getMembers($dep));
    	die();
    }
}