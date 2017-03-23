<?php

namespace Tests\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TestsPlatformBundle:Default:index.html.twig');
    }
}
