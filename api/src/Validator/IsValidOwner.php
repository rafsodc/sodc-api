<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute]
class IsValidOwner extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'You can only create tickets for yourself.';
    public $anonymousMessage = 'Cannot set owner unless you are authenticated';
}
