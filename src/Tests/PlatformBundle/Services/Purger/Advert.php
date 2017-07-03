<?php
namespace Tests\PlatformBundle\Services\Purger;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;

class Advert
{
    private $entityManager;
    private $logger;

    public function __construct(EntityManager $entityManager, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Récupère les annonces qui n'ont pas reçu de candidatures et qui datent d'avant (date - X jours)
     * @param integer $days
     * @return void|number
     */
    public function purge($days)
    {
        if (empty($days)) {
            $this->logger->warning('Purge : Lancement de la fonction purge avec un paramètre days null');
            return;
        }

        $repository = $this->entityManager->getRepository('Tests\PlatformBundle\Entity\Advert');
        $adverts = $repository->getAdvertsWithoutApplications($days);

        if (empty($adverts)) {
            $this->logger->warning('Purge : Aucune annonce à supprimer');
            return;
        }

        $nbPurgedAdverts = 0;

        foreach ($adverts as $advert) {
            $this->entityManager->remove($advert);
            $nbPurgedAdverts++;
        }

        $this->entityManager->flush();

        return $nbPurgedAdverts;
    }
}
