<?php

// src/Tests/PlatformBundle/Controller/AdvertController.php

namespace Tests\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdvertController extends Controller
{
    public function indexAction()
    {
        return new Response("Notre propre Hello World !");
    }

    public function azulAction()
    {
        // Création d'un objet pour récuperer le contenu d'un template
        $templating = $this->get('templating');
        // Récupération du contenu du template azul.html.twig
        $content = $templating->render('TestsPlatformBundle:Advert:azul.html.twig', ['ville' => "Vgayeth"]);
        return new Response($content);
    }
}