<?php

namespace UKMNorge\TidBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UsersController extends Controller
{
    public function listAction()
    {	
    	$data = array();
    	$data['dServ'] = $this->get('UKM.department');
        return $this->render('UKMTidBundle:Users:list.html.twig', $data);
    }
}
