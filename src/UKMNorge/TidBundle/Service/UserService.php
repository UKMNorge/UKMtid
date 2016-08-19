<?php

namespace UKMNorge\TidBundle\Service;

use Exception;


class UserService {
	public function __construct($doctrine, $container) {
		$this->doctrine = $doctrine;
		$this->container = $container;
		$this->repo = $doctrine->getRepository("UKMTidBundle:User");
	}
	
	public function getMyEmployees( $me ) {
		return array(new tmp_user(), new tmp_user());
	}

	public function isLoggedIn() {
		#$role = $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED');
		#dump($role);
		return $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED');
	}
	public function get( $id ) {
		$currentUser = $this->getCurrent();

	    #var_dump($current);
	    # Hvis dette ikke er deg selv;
	    if($currentUser->getId() != $id) {
	    	# Er enten super-admin;
	    	if ($this->container->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
	    		throw new Exception('Not implemented, super admin');
	    	} 
	    	# Eller department manager for riktig department.
	    	elseif($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
	    		throw new Exception('Not implemented, role_admin');
	    	}
	    	else {
	    		echo $this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
	    		throw $this->container->createAccessDeniedException();
	    	}
	    }
	    try {
		    $user = $this->repo->findOneBy(array("id" => $id));
		} catch ( Exception $e ) {
			throw new Exception('Brukeren finnes ikke:<br>' . $e, 1);
		}
		
		return $user;
	}
	
	public function getMyEmployees( $leader ) {
		$dServ = $this->container->get('UKM.department');
		
		$employees = array();

		if( $leader->isSuperUser() ) {
			// List all departments as well
			$deps = $dServ->getDepartments();
			for ($deps as $dep) {
				$employees[] = ($dep->getMembers());
			}
		} else if ($leader->isDepartmentManager() ) {
			$dep = $dServ->getDepartment($leader);
			$employees[] = $dep->getMembers();
		}

		return $employees;
	}

	public function getCurrent() {
		return $this->container->get('security.token_storage')->getToken()->getUser();
	}
}

class tmp_user {
	// TODO: LOGIC
	public function getId() {
		return 1;
	}
	
	// TODO: LOGIC
	public function isDepartmentManager() {
		return true;
	}
	
	public function isSuperUser() {
		return true;
	}
	public function getName() {
		return 'Marius Mandal';
	}
	
	public function getPercentage() {
		return 100;
	}

}