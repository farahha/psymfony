<?php

// src/Tests/PlatformBundle/Controller/AdvertController.php

namespace Tests\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if ($page < 1){
            return new NotFoundHttpException('Page N° ' . $page . ' introuvable !');
        }
        // Appelle du template
        return $this->render('TestsPlatformBundle:Advert:index.html.twig');
    }

    public function addAction(Request $request)
    {
        if ($request->isMethod('POST')){
            return $this->redirectToRoute('tests_platform_view', ['id' => 5]); // le 5 c'est juste pour le test
        }

        return $this->render('TestsPlatformBundle:Advert:add.html.twig');
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
        return $this->render('TestsPlatformBundle:Advert:view.html.twig', ['id' => $id]);
    }
}