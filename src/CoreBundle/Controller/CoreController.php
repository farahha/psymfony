<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreController extends Controller
{
    public function indexAction()
    {
        return $this->render('CoreBundle:Core:index.html.twig');
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
