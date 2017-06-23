<?php
namespace Tests\PlatformBundle\Services\Email;

use Tests\PlatformBundle\Entity\Application;

class ApplicationMailer
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(Application $application)
    {
        $subject = 'Vous avez reçu une nouvelle candidature';
        $body    = 'Bonjour, Vous venez de recevoir une nouvelle candidature à propos de votre annonce ('.$application->getAdvert()->getTitle().')';
        $message = new \Swift_Message($subject, $body);

        $message->addTo('kabyliXX@gmail.com', $application->getAdvert()->getAuthor());
        $message->addFrom('sofiane.sadoud@gmail.com', $application->getAuthor());

        $this->mailer->send($message);
    }
}
