<?php

// src/Tests/PlatformBundle/Controller/AdvertController.php

namespace Tests\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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

    public function viewAction($id, Request $request)
    {
        $session = $request->getSession(); // Récupération de la session
        $userId = $session->get('users_id');
        $session->set('users_id', $id);
        return new Response('test session : users_id en cours ' . $userId . ' sinon ... il faut aller voir dans les paramètres de la session et chercher le users_id pour voir la nouvelle valeur');
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