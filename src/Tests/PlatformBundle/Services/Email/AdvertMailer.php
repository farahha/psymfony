<?php
namespace Tests\PlatformBundle\Services\Email;

use Tests\PlatformBundle\Entity\Advert;

class AdvertMailer
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(Advert $advert, $action = null)
    {
        switch ($action) {
            case 'postPersist': // Ajout
                $subject = 'Création de votre annonce - ' . $advert->getTitle();
                $body    = 'Bonjour, Votre annonce ('.$advert->getTitle().') a bien été enregistrée et sera prochainement mise en ligne.';
                break;
            case 'postUpdate': // Après modification
                $subject = 'Modification de votre annonce - ' . $advert->getTitle();
                $body    = 'Bonjour, Votre annonce ('.$advert->getTitle().') a bien été modifiée.';
                break;
            case 'preRemove': // avant Suppression
                $subject = 'Suppression de votre annonce - ' . $advert->getTitle();
                $body    = 'Bonjour, Votre annonce ('.$advert->getTitle().') a bien été supprimée et ne sera plus accessible prochainement';
                break;
            default:
                $subject = 'Votre annonce - ' . $advert->getTitle();
                $body    = 'Bonjour, Votre annonce ('.$advert->getTitle().') a bien été enregistrée';
        }

        $message = new \Swift_Message($subject, $body);

        $message->addTo('kabyliXX@gmail.com', $advert->getAuthor());
        $message->addFrom('sofiane.sadoud@gmail.com', $advert->getAuthor());

        $this->mailer->send($message);
    }
}
