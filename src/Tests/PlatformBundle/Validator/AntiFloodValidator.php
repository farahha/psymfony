<?php

namespace Tests\PlatformBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Annotation
 */
class AntiFloodValidator extends ConstraintValidator
{
    private $requestStack;
    private $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        // Récuperation de l'objet Request
        $request = $this->requestStack->getCurrentRequest();

        // Récupération de l'ip
        $ip = $request->getClientIp();

        // on vérifie si avec l'ip récupérée, une candidature a déjà été envoyée il y a moins de 15sec
        $isFlood = $this->em->getRepository('Tests\PlatformBundle\Entity\Application')->isFlood($ip, 15);

        if ($isFlood) {
            $this->context->addViolation($constraint->message); // On déclanche l'erreur pour le formulaire
        }
    }
}
