<?php

namespace UKMNorge\TidBundle\Service;

use Exception;
use UKMNorge\TidBundle\Entity\User;

class UserService {
	public function __construct($doctrine, $container) {
		$this->timer = $container->get('UKM.timer');
		$this->timer->start('UserService::__construct()');
		$this->doctrine = $doctrine;
		$this->container = $container;
		$this->repo = $doctrine->getRepository("UKMTidBundle:User");
		
		$this->logger = $container->get('logger');
		$this->timer->stop('UserService::__construct()');
	}

	public function isLoggedIn() {
		// TODO: Hvorfor looper denne når man ikke er innlogget? (/validate)
		$this->logger->info('UKMTidBundle: Sjekker om brukeren er logget inn...');
		$isLoggedIn = $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED');
		$this->logger->info('UKMTidBundle: Brukeren var '. $isLoggedIn ? '' : ' ikke ' . 'logget inn.' );
		return $isLoggedIn;
	}

	public function get( $id ) {
		$currentUser = $this->getCurrent();

	    # Hvis dette ikke er deg selv;
	    if($currentUser->getId() != $id) {
	    	# og du ikke er enten super-admin;
	    	if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
	    		throw $this->container->createAccessDeniedException();
	    	} 
	    	# Eller department manager for riktig department.
	    	elseif($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
	    		// TODO: Sjekk at brukeren er department-leader for brukeren vi prøver å få tak i sin Department.
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
	
	// Returnerer et array med Departments
	public function getMyDepartments() {
		$dServ = $this->container->get('UKM.department');
		$user = $this->getCurrent();

		$deps = array();
		if($user->isSuperUser()) {
			$deps = $dServ->getDepartments();
		}
		else
			$deps[] = $user->getDepartment();
		return $deps;
	}

	public function getMyEmployees( $leader ) {
		$this->timer->start();
		$dServ = $this->container->get('UKM.department');
		
		$employees = array();

		if( $leader->isSuperUser() ) {
			// List all departments as well
			$deps = $dServ->getDepartments();
			foreach ($deps as $dep) {
				$employees = array_merge($employees, $dep->getMembers());
			}
		} else if ($leader->isDepartmentManager() ) {
			$dep = $dServ->getDepartment($leader);
			$employees = $dep->getMembers();
		}
		
		$this->timer->stop();

		return $employees;
	}

	public function getCurrent() {
		$user = $this->container->get('security.token_storage')->getToken()->getUser();
		if(!is_object($user)) {
			$user = $this->getByDeltaId($user);
		}

		return $user;
	}

	public function getByDeltaId($id) {
		return $this->repo->findOneBy(array('deltaId' => $id));
	}

	public function hasDepartment(User $user) {
		if(null != $user->getDepartment())
			return true;
		return false;
	}

	public function getUsers() {
		if( $this->getCurrent()->isSuperUser() ) {
			$this->timer->start('getUsers');
			
			$users = $this->repo->findAll(); 
			
			$this->timer->stop('getUsers');

			return $users;
		}
		else throw new Exception('UKMTidBundle: User not allowed to list all users. Need to be superuser.');
	}

	/**
	 * En bruker er ikke godkjent før den har blitt gitt en Department av en SuperAdmin, eller har angitt arbeidstid i prosent.
	 * Den er heller ikke gyldig dersom navnet er tomt.
	 */
	public function isValid(User $user = null) {
		if(!$user)
			$user = $this->getCurrent();
		$valid = true;
		if(!$this->hasDepartment($user))
			$valid = false;
		elseif(!$user->getPercentage())
			$valid = false;
		elseif(!$user->getName())
			$valid = false;

		if($user->isSuperUser())
			$valid = true;

		return $valid;
	}

	public function setExcludeHolidays(User $user, $value) {
		if(!is_bool($value))
			throw new Exception('UKMTidBundle: excludeHolidays må være en boolsk verdi.');
		$user->setExcludeHolidays($value);

		$this->doctrine->getManager()->persist($user);
		$this->doctrine->getManager()->flush();
		return true;
	}

	public function setPercentage(User $user, $percentage) {
		$error = null;
		if(!is_numeric($percentage))
			$error = 'UKMTidBundle: Stillingsprosent må være et tall mellom 0 og 100.';
		elseif($percentage < 0 || $percentage > 100)
			$error = 'UKMTidBundle: Stillingsprosent må være et tall mellom 0 og 100.';

		if ($error) {
			$this->logger->error($error);
			throw new Exception($error);
		}

		$user->setPercentage($percentage);
		$this->doctrine->getManager()->persist($user);
		$this->doctrine->getManager()->flush();
		return true;
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