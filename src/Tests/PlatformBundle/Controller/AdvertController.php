<?php

// src/Tests/PlatformBundle/Controller/AdvertController.php

namespace Tests\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\PlatformBundle\Entity\Advert;
use Tests\PlatformBundle\Entity\Image;
use Tests\PlatformBundle\Entity\AdvertSkill;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if (empty($page) || $page < 1){
            throw new NotFoundHttpException('Page N° "' . $page . '" introuvable !');
        }

        $repository = $this->getDoctrine()->getManager()->getRepository('Tests\PlatformBundle\Entity\Advert');
        $listAdverts = $repository->findAll();

        //return $this->render('TestsPlatformBundle:Advert:index.html.twig', ['page' => $page]);
        return $this->render('TestsPlatformBundle:Advert:index.html.twig', array(
                'listAdverts' => $listAdverts
        ));
    }

    public function addAction(Request $request)
    {
        // Juste pour remplir mes annonces
        $level = ['Junior', 'Confirmé', 'Expert'];
        $speciality = ['Back end PHP5 ZF2','Back end PHP5 SF3','Front end JS', 'Front end React'];
        $title = $speciality[round(rand(0,3))];

        // création d'une entité Advert
        $advert = new Advert();
        $advert->setTitle($title);
        $advert->setAuthor('KabyliXX');
        $advert->setContent('Nous recherchons pour notre entreprise spécialisée dans le développement un développeur '.$title);

        // Création d'une image
        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt("Je n'ai pas trouvé d'image");

        // Setter la prepriété de l'annonce avec l'objet Image
        $advert->setImage($image);

        // On va récupérer l'entité manager pour persister l'entité advert
        $em = $this->getDoctrine()->getManager();

        // Récupération du repo
        $repository = $this->getDoctrine()->getRepository("Tests\PlatformBundle\Entity\Skill");

        // récupération de tous les skills
        $listSkills = $repository->findAll();

        foreach ($listSkills as $skill){
            // Pour chaque skill, on crée une relation avec l'annonce
            $advertSkill = new AdvertSkill();
            // On lie la relation avec l'annonce
            $advertSkill->setAdvert($advert);
            // on lie la compétence avec l'annonce
            $advertSkill->setSkill($skill);
            // On lui attribue un niveau
            $advertSkill->setLevel($level[round(rand(0,2))]); // Oui c'est pas joli :/
            // On persiste la relation
            $em->persist($advertSkill);
        }

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
        if (empty($id)){
            throw new NotFoundHttpException("Pas d'id de renseigné.");
        }

        $repository = $this->getDoctrine()->getRepository('Tests\PlatformBundle\Entity\Advert');

        $advert = $repository->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        $em = $this->getDoctrine()->getManager();

        // La méthode findAll retourne toutes les catégories de la base de données
        $listCategories = $em->getRepository('Tests\PlatformBundle\Entity\Category')->findAll();

        // On boucle sur les catégories pour les lier à l'annonce
        foreach ($listCategories as $category) {
            $advert->addCategory($category);
        }

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

        // Étape 2 : On déclenche l'enregistrement
        $em->flush();

        if ($request->isMethod('POST')){
            // Code pour traiter la requête ...
            $this->addFlash('Info', 'Annonce '.$id.' bien modifiée');
            return $this->redirectToRoute('tests_platform_view', ['id' => $id]);
        }

        return $this->render('TestsPlatformBundle:Advert:edit.html.twig', array(
            'advert' => $advert
        ));
    }

    public function deleteAction($id)
    {
        if (empty($id)){
            throw new NotFoundHttpException("Empty advert ID");
        }

        $repository = $this->getDoctrine()->getRepository('Tests\PlatformBundle\Entity\Advert');

        $advert = $repository->find($id);

        if (null === $advert){
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
        $repository = $this->getDoctrine()->getManager()->getRepository('Tests\PlatformBundle\Entity\Advert');

        $advert = $repository->find($id);

        if (null === $advert)
        {
            throw new NotFoundHttpException("L'id de l'annonce $id n'existe pas");
        }

        // On récupère la liste des candidatures
        $applications = $this->getDoctrine()->getRepository("Tests\PlatformBundle\Entity\Application")->findBy(['advert' => $advert]);

        // On récupère les compétences
        $advertSkills = $this->getDoctrine()->getRepository("Tests\PlatformBundle\Entity\AdvertSkill")->findBy(['advert' => $advert]);

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

    // Fonction permettant de rajouter un message falsh
    protected function addFlash($type, $message)
    {
        if (!$this->container->has('session')) {
            throw new \LogicException('Impossible d\'utiliser les messages flash, le container n\'a pas démarré sa session.');
        }

        $this->container->get('session')->getFlashBag()->add($type, $message);
    }
}