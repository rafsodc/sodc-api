<?php

namespace App\Validator;

use App\Entity\Basket;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsBasketFreeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\IsBasketFree */

        if (null === $value || '' === $value) {
            return;
        }

        // If the value is not an instance of event, then throw an exception.
        if (!$value instanceof Basket) {
            throw new \InvalidArgumentException('@IsBasketFree constraint must be put on a property containing a Basket object');
        }

        if (!$value->getIsTransaction()) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
