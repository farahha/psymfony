<?php
namespace Tests\PlatformBundle\AntiSpam;

class AntiSpam
{
    private $mailer;
    private $locale;
    private $minLength;

    public function __construct(\Swift_Mailer $mailer, $locale, $minLength)
    {
        $this->mailer = $mailer;
        $this->locale = $locale;
        $this->minLength = (int) $minLength;
    }

    public function isSpam($text)
    {
        return strlen($text) < $this->minLength; // Si le texte fait moins de minLength caractère on dit que c'est un spam
    }
}