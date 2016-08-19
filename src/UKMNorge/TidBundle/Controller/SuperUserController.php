<?php

namespace UKMNorge\TidBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SuperUserController extends Controller
{
    public function disabledAction()
    {
        return $this->render('UKMTidBundle:SuperUser:disabled.html.twig');
    }
}