<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreController extends Controller
{
    public function indexAction()
    {
        // Vue que pour le moment, je n'ai pas encore connecté mon appli à une BDD,
        // je vais mettre comme pour le reste de l'appli, les annonces à affichier dans la page d'accueil en dur :/
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
        return $this->render('CoreBundle:Core:index.html.twig', array(
                'listAdverts' => $listAdverts
        ));
    }

    public function contactAction()
    {
        //return $this->render('CoreBundle:Core:contact.html.twig'); // Une fois le formulaire prêt, je branche sur ce template (actuellement vide)

        if (!$this->container->has('session')) {
            throw new \LogicException('Impossible d\'utiliser les messages flash, le contaner n\'a pas démarré sa session.');
        }
        $this->container->get('session')->getFlashBag()->add('info', "Message flash : La page de contact n'est pas encore disponible. Merci de revenir plus tard");

        return $this->redirectToRoute('core_homepage');
    }
}
