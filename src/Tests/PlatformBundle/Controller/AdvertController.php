<?php

// src/Tests/PlatformBundle/Controller/AdvertController.php

namespace Tests\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\PlatformBundle\Entity\Advert;
use Tests\PlatformBundle\Entity\Image;
use Tests\PlatformBundle\Entity\AdvertSkill;
use Tests\PlatformBundle\Entity\Application;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            // (Nous verrons la validation des objets en détail dans le prochain chapitre)
            if ($form->isValid()) {
                // On enregistre notre objet $advert dans la base de données, par exemple
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();

                $this->addFlash('notice', 'Annonce bien enregistrée.');

                // On redirige vers la page de visualisation de l'annonce nouvellement créée
                return $this->redirectToRoute('tests_platform_view', array('id' => $advert->getId()));
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau

        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('TestsPlatformBundle:Advert:add.html.twig', array(
            'form' => $form->createView(),
        ));
/*
        // Juste pour remplir mes annonces
        $level = ['Junior', 'Confirmé', 'Expert'];
        $speciality = ['Back end PHP5 ZF2','Back end PHP5 SF3','Front end JS', 'Front end React'];
        $urls = [
            'http://www.business-agility.com/wp-content/uploads/2014/07/850x236-implementation.jpg',
            'http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg',
            'http://www.business-agility.com/wp-content/uploads/2014/07/850x236-events.jpg',
            'http://www.karmatechnologies.asia/wp-content/uploads/2015/11/Web-Applications.jpg',
        ];
*/
    }

    public function editAction($id, Request $request)
    {
        if (empty($id)) {
            throw new NotFoundHttpException("Pas d'id de renseigné.");
        }

        $repository = $this->getDoctrine()->getRepository('Tests\PlatformBundle\Entity\Advert');

        $advert = $repository->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        $form = $this->createForm(AdvertEditType::class, $advert);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();

                $this->addFlash('notice', 'Annonce mise à jour.');

                return $this->redirectToRoute('tests_platform_view', array('id' => $advert->getId()));
            }
        }

        return $this->render('TestsPlatformBundle:Advert:edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteAction($id)
    {
        if (empty($id)) {
            throw new NotFoundHttpException("Empty advert ID");
        }

        $repository = $this->getDoctrine()->getRepository('Tests\PlatformBundle\Entity\Advert');

        $advert = $repository->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException('Aucune annonce ne correspond à cet Id : '.$id);
        }

        $em = $this->getDoctrine()->getManager();

        // On boucle sur les catégories de l'annonce pour les supprimer
        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }

        // On déclenche la modification
        $em->flush();

        return $this->render('TestsPlatformBundle:Advert:delete.html.twig', ['advert' => $advert]);
    }


    public function viewAction($id)
    {
        // On récupère le repo de Advert Entity
        $repository = $this->getDoctrine()->getManager()->getRepository('TestsPlatformBundle:Advert');

        $advert = $repository->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'id de l'annonce $id n'existe pas");
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
        $em = $this->getDoctrine()->getManager();
        $rAdv = $em->getRepository('TestsPlatformBundle:Advert');
        $rApp = $em->getRepository('TestsPlatformBundle:Application');

        $advert1 = $rAdv->findOneBy(['author' => 'kabyliXX']);
        //$advert1->setAuthor('Nap');

        $application = new Application();
        $application->setAdvert($advert1);
        $application->setAuthor('YYYYYY');
        $application->setContent('Bonjour, Je suis intéressé par le poste que vous proposez.');
        $application->setDate(new \DateTime());


        $em->persist($advert1);
        $em->persist($application);

        $em->flush();

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
