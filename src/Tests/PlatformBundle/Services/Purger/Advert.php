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
     *
     * @param integer $days
     * @return void|number
     */
    public function purge($days)
    {
        $this->logger->error('Purge : Aucune annonce à supprimer');

        if (empty($days)) {
            $this->logger->info('Purge : Lancement de la fonction purge avec un paramètre days null');
            return;
        }

        $repository = $this->entityManager->getRepository('Tests\PlatformBundle\Entity\Advert');
        $adverts = $repository->getAdvertsWithoutApplications($days);

        if (empty($adverts)) {
            $this->logger->info('Purge : Aucune annonce à supprimer');
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
