<?php

namespace UKMNorge\TidBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UsersController extends Controller
{
    public function listAction()
    {
        return $this->render('UKMTidBundle:Users:list.html.twig');
    }
}
