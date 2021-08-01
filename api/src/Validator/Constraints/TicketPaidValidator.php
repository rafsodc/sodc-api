<?php
// api/src/Validator/Constraints/CaptchaValidator.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Annotation
 */
final class TicketPaidValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($object, Constraint $constraint): void
    {
        // Get the existing ticket to compare
        $oldObject = $this->em->getUnitofWork()->getOriginalEntityData($object);

        // If there is an existing ticket
        if(is_array($oldObject) && !empty($oldObject)) {
            // And it is paid
            if($oldObject['paid']) {
                // If the ticket_type id has changed
                if($oldObject['ticket_type_id'] !== $object->getTicketType()->getId()) {
                    // Result in a violation
                    $this->context->buildViolation($constraint->message)->addViolation();
                }
            }
        }
    }
}
