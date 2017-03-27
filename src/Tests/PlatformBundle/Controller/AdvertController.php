<?php

// src/Tests/PlatformBundle/Controller/AdvertController.php

namespace Tests\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        return new Response("Récupération de la page N° " . $page);
    }

    public function addAction()
    {
        return new Response("Ajouter ? Quoi ???? ... bon bah j'attends des données moi !");
    }

    public function editAction($id)
    {
        return new Response("Modifier ? Quoi ???? ... bah l'annonce dont l'id est : ". $id);
    }

    public function deleteAction($id)
    {
        return new Response("Supprimer ? Quoi ???? ... bah l'annonce dont l'id est : " . $id);
    }

    public function viewAction($id)
    {
        $response = new Response();
        $response->setContent('Ceci est une page erreur 404');
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        return $response;
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