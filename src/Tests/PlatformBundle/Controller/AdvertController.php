<?php

// src/Tests/PlatformBundle/Controller/AdvertController.php

namespace Tests\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if (empty($page) || $page < 1){
            throw new NotFoundHttpException('Page N° "' . $page . '" introuvable !');
        }
        return $this->render('TestsPlatformBundle:Advert:index.html.twig', ['page' => $page]);
    }

    public function addAction(Request $request)
    {
        if ($request->isMethod('POST')){
            // Code pour traiter la requête ...
            $this->addFlash('info', 'Annonce bien enregistrée');
            return $this->redirectToRoute('tests_platform_view', ['id' => 5]); // le 5 c'est juste pour le test
        }

        return $this->render('TestsPlatformBundle:Advert:add.html.twig');
    }

    public function editAction($id, Request $request)
    {
        if ($request->isMethod('POST')){
            // Code pour traiter la requête ...
            $this->addFlash('Info', 'Annonce '.$id.' bien modifiée');
            return $this->redirectToRoute('tests_platform_view', ['id' => $id]);
        }

        return $this->render('TestsPlatformBundle:Advert:edit.html.twig');
    }

    public function deleteAction($id)
    {
        // Code pour traiter la requête ...
        return $this->render('TestsPlatformBundle:Advert:delete.html.twig', ['id' => $id]);
    }

    public function viewAction($id)
    {
        // Code pour traiter la requête ...
        return $this->render('TestsPlatformBundle:Advert:view.html.twig', ['id' => $id]);
    }

    // Fonction permettant de rajouter un message falsh
    protected function addFlash($type, $message)
    {
        if (!$this->container->has('session')) {
            throw new \LogicException('Impossible d\'utiliser les messages flash, le contaner n\'a pas démarré sa session.');
        }

        $this->container->get('session')->getFlashBag()->add($type, $message);
    }
}