<?php

// src/Tests/PlatformBundle/Controller/AdvertController.php

namespace Tests\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\PlatformBundle\Entity\Advert;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if (empty($page) || $page < 1){
            throw new NotFoundHttpException('Page N° "' . $page . '" introuvable !');
        }

        // Notre liste d'annonce en dur

        $listAdverts = array(
            array(
                'title'   => 'Recherche développpeur Symfony',
                'id'      => 1,
                'author'  => 'Alexandre',
                'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
                'date'    => new \Datetime()),
            array(
                'title'   => 'Mission de webmaster',
                'id'      => 2,
                'author'  => 'Hugo',
                'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
                'date'    => new \Datetime()),
            array(
                'title'   => 'Offre de stage webdesigner',
                'id'      => 3,
                'author'  => 'Mathieu',
                'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
                'date'    => new \Datetime())
        );
        //return $this->render('TestsPlatformBundle:Advert:index.html.twig', ['page' => $page]);
        return $this->render('TestsPlatformBundle:Advert:index.html.twig', array(
                'listAdverts' => $listAdverts
        ));
    }

    public function addAction(Request $request)
    {
        // création d'une entité Advert
        $advert = new Advert();
        $advert->setTitle('Titre de mon annonce');
        $advert->setAuthor('KabyliXX');
        $advert->setContent('Un contenu pour remplir le corps de mon annonce ... ça en fait trop là');
        $advert->setDate(new \DateTime());

        // On va récupérer l'entité manager pour persister l'entité advert
        $em = $this->getDoctrine()->getManager();

        // 1 - Persister l'entité (<=> preparation de la requete)
        // A noter aussi : Persist fonctionne comme une transaction dans laquelle on met differentes requetes a executer
        $em->persist($advert);

        // 2 - flusher tout ce qui a été persisté (<=> a l'execution des requetes preparées INSERT, UPDATE, DELETE ...)
        // Le flush comme un commit, valide les requetes contenues dans le persist et si y a pas d'erreur c'est OK sinon rollback
        $em->flush();

        if ($request->isMethod('POST')){
            // Code pour traiter la requête ...
            $this->addFlash('info', 'Annonce bien enregistrée');
            return $this->redirectToRoute('tests_platform_view', ['id' => 5]); // le 5 c'est juste pour le test
        }
/*
        $antispam= $this->container->get('tests_platform.antispam');
        $txt = 'un deux ...';
        if ($antispam->isSpam($txt))
        {
            throw new \Exception("Vous annonce a été détecté comme spam ...");
        }
*/
        return $this->redirectToRoute('tests_platform_view', array('id' => $advert->getId()));

        //return $this->render('TestsPlatformBundle:Advert:add.html.twig');
    }

    public function editAction($id, Request $request)
    {
        if ($request->isMethod('POST')){
            // Code pour traiter la requête ...
            $this->addFlash('Info', 'Annonce '.$id.' bien modifiée');
            return $this->redirectToRoute('tests_platform_view', ['id' => $id]);
        }

        $advert = array(
            'title'   => 'Recherche développpeur Symfony',
            'id'      => $id,
            'author'  => 'Alexandre',
            'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
            'date'    => new \Datetime()
        );

        return $this->render('TestsPlatformBundle:Advert:edit.html.twig', array(
                'advert' => $advert
        ));
    }

    public function deleteAction($id)
    {

        $advert = array(
            'title'   => 'Recherche développpeur Symfony',
            'id'      => $id,
            'author'  => 'Alexandre',
            'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
            'date'    => new \Datetime()
        );
        return $this->render('TestsPlatformBundle:Advert:delete.html.twig', ['advert' => $advert]);
    }

    public function viewAction($id)
    {
        // On récupère le repo de Advert Entity
        $repository = $this->getDoctrine()->getManager()->getRepository('Tests\PlatformBundle\Entity\Advert');
        $conn  = $this->getDoctrine()->getConnection('name')

        $advert = $repository->find($id);

        if (null === $advert)
        {
            throw new NotFoundHttpException("L'id de l'annonce $id n'existe pas");
        }

        return $this->render('TestsPlatformBundle:Advert:view.html.twig', array(
            'advert' => $advert
        ));
    }

    public function menuAction($limit)
    {
        $repository = $this->getDoctrine()->getRepository('Tests\PlatformBundle\Entity\Advert');
        $lastAdverts = $repository->findBy([], ['date' => 'desc'], 3, null);

        return $this->render('TestsPlatformBundle:Advert:menu.html.twig', array(
            // Tout l'intérêt est ici : le contrôleur passe
            // les variables nécessaires au template !
            'listAdverts' => $lastAdverts
        ));
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