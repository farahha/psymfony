<?php
namespace Library\PlatformBundle\Services\AntiSpam;

class AntiSpam
{
    private $mailer;
    private $minLength;

    public function __construct(\Swift_Mailer $mailer, $minLength)
    {
        $this->mailer = $mailer;
        $this->minLength = (int) $minLength;
    }

    public function isSpam($text)
    {
        if (strlen($text) < $this->minLength) {
            // Ici je peux imagner envoyer un mail d'alerte en utilisant mailer ...
            return true;
        }

        return false;
    }
}
