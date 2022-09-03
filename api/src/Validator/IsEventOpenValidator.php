<?php

namespace App\Validator;

use App\Entity\Event;
use DateTime;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsEventOpenValidator extends ConstraintValidator
{
    private Security $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\IsEventOpen */

        if (null === $value || '' === $value) {
            return;
        }

        // Allow admins to create a ticket for an event, event if it's closed.
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        // If the value is not an instance of event, then throw an exception.
        if (!$value instanceof Event) {
            throw new \InvalidArgumentException('@IsValidOwner constraint must be put on a property containing a User object');
        }

        $now = new DateTime();
        if ($value->getBookingOpen() <= $now && $value->getBookingClose() >= $now) {
            return;
        }

        // if ($value->getIsBookingOpen()) {
        //     return;
        // }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
