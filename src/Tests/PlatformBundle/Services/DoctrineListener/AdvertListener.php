<?php
namespace Tests\PlatformBundle\Services\DoctrineListener;

use Tests\PlatformBundle\Services\Email\AdvertMailer;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Tests\PlatformBundle\Entity\Advert;

class AdvertListener
{
    /**
     *
     * @var AdvertMailer
     */
    private $advertMailer;
    private $env;

    public function __construct(AdvertMailer $advertMailer, $env)
    {
        $this->advertMailer = $advertMailer;
        $this->env = $env;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Advert) {
            return;
        }

        try {
            $this->advertMailer->sendEmail($entity, __FUNCTION__);
        } catch (\Exception $e) {
            // Next time
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Advert) {
            return;
        }

        try {
            $this->advertMailer->sendEmail($entity, __FUNCTION__);
        } catch (\Exception $e) {
            // Next time
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Advert) {
            return;
        }

        try {
            $this->advertMailer->sendEmail($entity, __FUNCTION__);
        } catch (\Exception $e) {
            // Next time
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Advert) {
            return;
        }

        try {
            $this->advertMailer->sendEmail($entity, __FUNCTION__);
        } catch (\Exception $e) {
            // Next time
        }
    }
}
