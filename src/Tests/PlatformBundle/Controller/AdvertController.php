<?php

// src/Tests/PlatformBundle/Controller/AdvertController.php

namespace Tests\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    public function viewAction($id)
    {
        return new Response("L'id rentrée est ". $id);
    }

    public function viewSlugAction($slug, $year, $_format, $_locale)
    {
        return new Response("On pourrait afficher l'annonce correspondant au slug ($_locale) '".$slug."', créée en ".$year." et au format ".$_format.".");
    }
}