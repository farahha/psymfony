<?php

namespace Library\PlatformBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AntiFlood extends Constraint
{
    public $message = 'Vous avez déjà envoyé un message il y a moins de 15 secondes. Merci de patienter avant d\'envoyer un autre message.';

    public function validatedBy()
    {
        return 'library_platform_antiflood';
    }
}
