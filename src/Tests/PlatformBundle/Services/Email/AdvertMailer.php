<?php
namespace Tests\PlatformBundle\Services\Email;

use Tests\PlatformBundle\Entity\Advert;

class AdvertMailer
{
    private $mailer;
    private $env;

    public function __construct(\Swift_Mailer $mailer, $env)
    {
        $this->mailer = $mailer;
        $this->env = $env;
    }

    public function sendEmail(Advert $advert, $action = null)
    {
        $subject = 'Votre annonce - ' . $advert->getTitle();
dump($this->env);
        switch ($action)
        {
            case 'postPersist': // Ajout
                $body    = 'Bonjour, Votre annonce ('.$advert->getTitle().') a bien été enregistrée et sera prochainement mise en ligne.';
                break;
            case 'postUpdate': // Après modification
                $body    = 'Bonjour, Votre annonce ('.$advert->getTitle().') a bien été modifiée.';
                break;
            case 'preRemove': // Suppression
                $body    = 'Bonjour, Votre annonce ('.$advert->getTitle().') a bien été supprimée et ne sera plus accessible prochainement';
                break;
            default:
                $body    = 'Bonjour, Votre annonce ('.$advert->getTitle().') a bien été enregistrée';
        }

        $message = \Swift_Message::newInstance($subject, $body);

        $message->addTo('kabyliXX@gmail.com', $advert->getAuthor());
        $message->addFrom('sofiane.sadoud@gmail.com', $advert->getAuthor());

        $this->mailer->send($message);
    }
}
