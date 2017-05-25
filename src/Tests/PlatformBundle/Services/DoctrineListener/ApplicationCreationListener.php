<?php
namespace Tests\PlatformBundle\Services\DoctrineListener;

use Tests\PlatformBundle\Services\Email\ApplicationMailer;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Tests\PlatformBundle\Entity\Application;

class ApplicationCreationListener
{
    /**
     *
     * @var ApplicationMailer
     */
    private $applicationMailer;

    public function __construct(ApplicationMailer $applicationMailer)
    {
        $this->applicationMailer = $applicationMailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Application) {
            return;
        }

        try {
            $this->applicationMailer->sendEmail($entity);
        } catch (\Exception $e){
            // Next time
        }
    }
}
