<?php
namespace Tests\PlatformBundle\Services\DoctrineListener;

use Tests\PlatformBundle\Services\Email\AdvertMailer;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Tests\PlatformBundle\Entity\Advert;
use Symfony\Component\HttpFoundation\RequestStack;

class AdvertListener
{
    /**
     *
     * @var AdvertMailer
     */
    private $advertMailer;
    private $requestStack;
    private $env;

    public function __construct(AdvertMailer $advertMailer, RequestStack $requestStack, $env)
    {
        $this->advertMailer = $advertMailer;
        $this->requestStack = $requestStack;
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

        // Récuperation de l'objet Request
        $request = $this->requestStack->getCurrentRequest();

        // Récupération de l'ip
        $ip = $request->getClientIp();

        $entity->setAdvertIpAddress($ip);
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

        $request = $this->requestStack->getCurrentRequest();
        // TODO : Filtrer uniquement sur les mise a jour venant de l'action update ...
        //$uri = $request->getUri();
        //dump($uri);

        try {
            $this->advertMailer->sendEmail($entity, __FUNCTION__);
        } catch (\Exception $e) {
            // Next time
        }
    }
}
