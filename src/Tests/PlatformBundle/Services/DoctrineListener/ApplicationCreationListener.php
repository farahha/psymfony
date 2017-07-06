<?php
namespace Tests\PlatformBundle\Services\DoctrineListener;

use Tests\PlatformBundle\Services\Email\ApplicationMailer;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Tests\PlatformBundle\Entity\Application;
use Symfony\Component\HttpFoundation\RequestStack;

class ApplicationCreationListener
{
    /**
     *
     * @var ApplicationMailer
     */
    private $applicationMailer;
    private $env;
    private $requestStack;

    public function __construct(ApplicationMailer $applicationMailer, RequestStack $requestStack, $env)
    {
        $this->applicationMailer = $applicationMailer;
        $this->requestStack = $requestStack;
        $this->env = $env;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Application) {
            return;
        }

        try {
            $this->applicationMailer->sendEmail($entity);
        } catch (\Exception $e) {
            // Next time
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Application) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        $ip = $request->getClientIp();
        $entity->setAplicationIpAddress($ip);
    }
}
