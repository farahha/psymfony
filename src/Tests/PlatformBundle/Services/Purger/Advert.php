<?php
namespace Tests\PlatformBundle\Services\Purger;

use Doctrine\ORM\EntityManager;

class Advert
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function purge($days)
    {
        if (empty($days)){
            return;
        }

        $repository = $this->entityManager->getRepository('Tests\PlatformBundle\Entity\Advert');
        $adverts = $repository->getAdvertWithConditions();

        return $adverts;

    }
}
