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
        $this->logger->info('lancement de la fonction purge');

        if (empty($days)) {
            return;
        }

        $repository = $this->entityManager->getRepository('Tests\PlatformBundle\Entity\Advert');
        $adverts = $repository->getAdvertsWithoutApplications($days);

        if (empty($adverts)) {
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
