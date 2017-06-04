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
        // On crée un objet Advert
        $advert = new Advert();

        // Ici, on préremplit avec la date d'aujourd'hui, par exemple
        // Cette date sera donc préaffichée dans le formulaire, cela facilite le travail de l'utilisateur
        $advert->setDate(new \Datetime());

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $advert);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
        ->add('date',      DateType::class)
        ->add('title',     TextType::class)
        ->add('content',   TextareaType::class)
        ->add('author',    TextType::class)
        ->add('published', CheckboxType::class, ['required' => false,])
        ->add('save',      SubmitType::class)
        ;
        // Pour l'instant, pas de candidatures, catégories, etc., on les gérera plus tard

        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();

        // Si la requête est en POST
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
        $title = $speciality[round(rand(0, 3))];

        if ($request->isMethod('POST')) {
            $antispam = $this->container->get('tests_platform.services.antispam');

            // Récupération des valeur envoyées dans le formulaire.
            $title = htmlspecialchars($request->get('title', $title)); // Le second parmètre est par défaut (pas clean)
            $author = htmlspecialchars($request->get('author', 'kabyliXX'));
            $content = htmlspecialchars($request->get('content', 'Nous recherchons pour notre entreprise spécialisée dans le développement un développeur '.$title));

            // création d'une entité Advert
            $advert = new Advert();
            $advert->setTitle($title);
            $advert->setAuthor($author);
            $advert->setContent($content);

            if ($antispam->isSpam($content)) {
                $advert->setPublished(false);
                //throw new \Exception("Vous annonce a été détecté comme spam ...");
            }

            // Création d'une image
            $image = new Image();
            $image->setUrl($urls[round(rand(0, 3))]);
            $image->setAlt("Je n'ai pas trouvé d'image");
            $advert->setImage($image);

            $em = $this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()->getRepository("Tests\PlatformBundle\Entity\Skill");

            // récupération de tous les skills
            $listSkills = $repository->findAll();
            foreach ($listSkills as $skill) {
                $advertSkill = new AdvertSkill();
                $advertSkill->setAdvert($advert);
                $advertSkill->setSkill($skill);
                $advertSkill->setLevel($level[round(rand(0, 2))]); // Oui c'est pas joli :/
                $em->persist($advertSkill);
            }

            $em->persist($advert);
            $em->flush();

            $this->addFlash('info', 'Votre annonce ('.$advert->getTitle().') a bien été enregistrée.');
            return $this->redirectToRoute('tests_platform_view', ['id' => $advert->getId()]); // le 5 c'est juste pour le test
        }

        $this->addFlash('info', 'Le formulaire d\'ajout d\'annonce n\'est pas encore créé.');
        return $this->render('TestsPlatformBundle:Advert:add.html.twig');
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
/*
        $em = $this->getDoctrine()->getManager();

        $listCategories = $em->getRepository('Tests\PlatformBundle\Entity\Category')->findAll();

        foreach ($listCategories as $category) {
            $advert->addCategory($category);
        }
*/
        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

        // Étape 2 : On déclenche l'enregistrement
        $em->flush();

        if ($request->isMethod('POST')) {
            $antispam = $this->container->get('tests_platform.antispam');

            // Récupération des valeur envoyées dans le formulaire.
            if (!empty($request->get('title'))) {
                $title = htmlspecialchars($request->get('title'));
            }
            if (!empty($request->get('author'))) {
                $author = htmlspecialchars($request->get('author'));
            }
            if (!empty($request->get('content'))) {
                $content = htmlspecialchars($request->get('content'));
            }

            $advert->setTitle($title);
            $advert->setAuthor($author);
            $advert->setContent($content);

            if ($antispam->isSpam($content)) {
                $advert->setPublished(false);
                //throw new \Exception("Vous annonce a été détecté comme spam ...");
            }

            $image = new Image();
            $image->setUrl($urls[round(rand(0, 3))]);
            $image->setAlt("Je n'ai pas trouvé d'image");
            $advert->setImage($image);

            $repository = $this->getDoctrine()->getRepository("Tests\PlatformBundle\Entity\Skill");

            // récupération de tous les skills
            $listSkills = $repository->findAll();
            foreach ($listSkills as $skill) {
                $advertSkill = new AdvertSkill();
                $advertSkill->setAdvert($advert);
                $advertSkill->setSkill($skill);
                $advertSkill->setLevel($level[round(rand(0, 2))]); // Oui c'est pas joli :/
                $em->persist($advertSkill);
            }

            $this->addFlash('Info', 'L\'annonce '.$advert->getTitle().' a bien été mise à jour');
            return $this->redirectToRoute('tests_platform_view', ['id' => $id]);
        }

        return $this->render('TestsPlatformBundle:Advert:edit.html.twig', array(
            'advert' => $advert
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
