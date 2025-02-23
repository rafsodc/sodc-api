<?php
// api/src/Validator/Constraints/Captcha.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute]
class TicketPaid extends Constraint
{
    public $message = 'Unable to change ticket type if ticket is already paid.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
