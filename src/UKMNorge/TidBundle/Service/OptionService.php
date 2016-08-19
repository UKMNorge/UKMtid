<?php

namespace UKMNorge\TidBundle\Service;

use Exception;
use UKMNorge\TidBundle\Entity\Option;

class OptionService {
	private $doctrine;
	private $repo;
	private $em;

	public function __construct($doctrine) {
		$this->doctrine = $doctrine;
		$this->repo = $doctrine->getRepository("UKMTidBundle:Option");
		$this->em = $this->doctrine->getManager();
	}

	public function get( $key ) {
		
		// DO GET
		
		$option = $this->repo->findOneBy(array("name"=> $key));
		if( null == $option ) {
			return null;
			#throw new Exception("Option not found.");
			return false;
		}
		return $option->getValue();
	}
	
	public function set( $key, $value ) {
		if( null == $value || false == $value || 'false' == $value ) {
			$this->delete( $key );
		}
		
		// DO SET
		$option = $this->repo->findOneBy(array("name" => $key));
		if (!$option) {
			$option = new Option();
			$option->setName($key);
		}
		$option->setValue($value);
		
		$this->em->persist($option);
		$this->em->flush();
		
		return $this;
	}
	
	public function delete( $key ) {
		
		// DO DELETE
		$option = $this->repo->findOneBy(array("name" => $key));

		if($option) {
			$this->em->remove($option);
			$this->em->flush();
		}
		return $this;
	}
}