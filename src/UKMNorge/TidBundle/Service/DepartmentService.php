<?php

namespace UKMNorge\TidBundle\Service;

use UKMNorge\TidBundle\Entity\Department;
use UKMNorge\TidBundle\Entity\User;

class DepartmentService {
	public function __construct($doctrine, $timer) {
		$this->timer = $timer;
		$this->timer->start('DepartmentService::__construct()');

		$this->doctrine = $doctrine;
		$this->em = $doctrine->getManager();
		$this->repo = $doctrine->getRepository("UKMTidBundle:Department");

		$this->timer->stop('DepartmentService::__construct()');
	}

	public function get($dep_id) {
		return $this->repo->findOneBy(array('id' => $dep_id));
	}

	public function getDepartments() {
		return $this->repo->findAll();
	}

	public function addDepartment($name) {
		$dep = new Department();
		$dep->setName($name);

		$this->em->persist($dep);
		$this->em->flush();
		return true;
	}

	public function addMember(Department $dep, User $user) {

		// TODO: Sjekk om brukeren er en del av department fra før av?

		$dep->addMember($user);
		$user->setDepartment($dep);

		$this->em->persist($user);
		$this->em->persist($dep);
		$this->em->flush();
		return true;
	}

	public function getMembers(Department $dep) {
		return $dep->getMembers();
	}

}