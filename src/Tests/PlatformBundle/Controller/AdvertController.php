<?php

namespace Tests\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\PlatformBundle\Entity\Advert;
use Tests\PlatformBundle\Entity\Image;
use Tests\PlatformBundle\Entity\AdvertSkill;
use Tests\PlatformBundle\Entity\Application;
use Tests\PlatformBundle\Form\AdvertType;
use Tests\PlatformBundle\Form\AdvertEditType;

class AdvertController extends Controller
{
    protected $sets = [
        'index' => [
            '__embedded' => [
                'applications' => [
                    'fields' => [], // je ne sais pas encore comment je peux limiter la récupération sur certains champs uniquement :/
                ],
                'skills' => [
                    'fields' => [],
                ],
                'categories' => [
                    'fields' => [],
                ],
                'image' => [
                    'fields' => [],
                ],
            ],
        ],
    ];

    public function indexAction($page)
    {
        if (empty($page)) {
            $page = 1;
        }

        if ($page < 1) {
            throw new NotFoundHttpException('Page N° "' . $page . '" introuvable !');
        }

        $nbAdvertPerPage = 4;

        $repository = $this->getDoctrine()->getManager()->getRepository('Tests\PlatformBundle\Entity\Advert');
        $listAdverts = $repository->getAdverts($page, $nbAdvertPerPage, null); // Retourne Paginator

        $nbPages = ceil(count($listAdverts) / $nbAdvertPerPage);

        if ($page > $nbPages) {
            throw new NotFoundHttpException('Page N° "' . $page . '" n\'existe pas !');
        }

        return $this->render('TestsPlatformBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts,
            'nbAdverts' => count($listAdverts), // le count(Paginator) donne le nombre tatal en BDD
            'nbPages' => (int) $nbPages,
            'page' => $page
        ));
    }

    public function addAction(Request $request)
    {
        $advert = new Advert();
        $form = $this->createForm(AdvertType::class, $advert);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($advert);
            $entityManager->flush();

            $this->addFlash('notice', 'Annonce bien enregistrée.');

            return $this->redirectToRoute('tests_platform_view', array('advertId' => $advert->getId()));
        }

        return $this->render('TestsPlatformBundle:Advert:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAction($advertId, Request $request)
    {
        if (empty($advertId)) {
            throw new NotFoundHttpException("Pas d'id de renseigné.");
        }

        $entityManager = $this->getDoctrine()->getManager();

        $advert = $entityManager->getRepository('Tests\PlatformBundle\Entity\Advert')->find($advertId);

        if ($advert === null) {
            throw new NotFoundHttpException("L'annonce d'id ".$advertId." n'existe pas.");
        }

        $form = $this->createForm(AdvertEditType::class, $advert);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $entityManager->flush();
            $this->addFlash('notice', 'L\'annonce a bien été mise à jour.');
            return $this->redirectToRoute('tests_platform_view', array('advertId' => $advert->getId()));
        }

        return $this->render('TestsPlatformBundle:Advert:edit.html.twig',
            [
                'form' => $form->createView(),
                'advert' => $advert,
            ]
        );
    }

    public function deleteAction($advertId, Request $request)
    {
        if (empty($advertId)) {
            throw new NotFoundHttpException("Empty advert ID");
        }
        $entityManager = $this->getDoctrine()->getManager();
        $advert = $entityManager->getRepository('Tests\PlatformBundle\Entity\Advert')->find($advertId);

        if ($advert === null) {
            throw new NotFoundHttpException('Aucune annonce ne correspond à cet Id : '.$advertId);
        }

        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $entityManager->remove($advert);
            $entityManager->flush();
            $this->addFlash('notice', 'L\'annonce a bien été supprimée.');
            return $this->redirectToRoute('tests_platform');
        }

        return $this->render('TestsPlatformBundle:Advert:delete.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }


    public function viewAction($advertId)
    {
        // On récupère le repo de Advert Entity
        $repository = $this->getDoctrine()->getManager()->getRepository('TestsPlatformBundle:Advert');

        $advert = $repository->find($advertId);

        if (null === $advert) {
            throw new NotFoundHttpException("L'id de l'annonce $advertId n'existe pas");
        }

        // On récupère la liste des candidatures
        $applications = $this->getDoctrine()->getRepository("TestsPlatformBundle:Application")
                        ->findBy(['advert' => $advert]);

        // On récupère les compétences
        $advertSkills = $this->getDoctrine()->getRepository("TestsPlatformBundle:AdvertSkill")
                        ->findBy(['advert' => $advert]);

        return $this->render('TestsPlatformBundle:Advert:view.html.twig', array(
            'advert' => $advert,
            'applications' => $applications,
            'advertSkills' => $advertSkills,
        ));
    }

    public function menuAction($limit)
    {
        $repository = $this->getDoctrine()->getRepository('Tests\PlatformBundle\Entity\Advert');
        $lastAdverts = $repository->findBy([], ['date' => 'desc'], $limit, null);

        return $this->render('TestsPlatformBundle:Advert:menu.html.twig', array(
            // Tout l'intérêt est ici : le contrôleur passe
            // les variables nécessaires au template !
            'listAdverts' => $lastAdverts
        ));
    }

    public function testAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $rAdv = $entityManager->getRepository('TestsPlatformBundle:Advert');

        $advert1 = $rAdv->findOneBy(['author' => 'kabyliXX']);

        $application = new Application();
        $application->setAdvert($advert1);
        $application->setAuthor('Sofiane S');
        $application->setContent('Bonjour, Je suis intéressé par le poste que vous proposez.');
        $application->setDate(new \DateTime());


        $entityManager->persist($advert1);
        $entityManager->persist($application);

        $entityManager->flush();

        $listAdverts = $rAdv->myFindAll();
        return $this->render('TestsPlatformBundle:Advert:index.html.twig', [
            'listAdverts' => $listAdverts,
            'nbAdverts' => count($listAdverts),
            'test' => [$advert1,$application],
        ]);
    }

    public function purgeAction($days)
    {
        $purgerService = $this->container->get('tests_platform.services.purger.advert');

        $nbPurgedAdverts = (int) $purgerService->purge($days);

        $this->addFlash('info', "L'action fait appel au service de purge pour supprimer les annonces qui n'ont pas reçu de candidatures");

        $date = new \DateTime();
        $date->sub(new \DateInterval('P'.(int)$days.'D'));

        return $this->render('TestsPlatformBundle:Advert:purge.html.twig', [
            'nbPurgedAdverts' => $nbPurgedAdverts,
            'date' => (new \DateTime())->sub(new \DateInterval('P'.(int)$days.'D')),
        ]);
    }

    // Fonction permettant de rajouter un message falsh
    protected function addFlash($type, $message)
    {
        if (!$this->container->has('session')) {
            throw new \LogicException('Impossible d\'utiliser les messages flash, le container n\'a pas démarré sa session.');
        }

        $this->container->get('session')->getFlashBag()->add($type, $message);
    }
}
