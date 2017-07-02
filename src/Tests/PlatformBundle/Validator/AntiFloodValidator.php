<?php

namespace Tests\PlatformBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class AntiFloodValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (strlen($value) < 3) {
            $this->context->addViolation($constraint->message); // On déclanche l'erreur pour le formulaire
        }
    }
}
