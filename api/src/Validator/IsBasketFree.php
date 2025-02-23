<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute]
class IsBasketFree extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'This basket is already assigned a transaction.';
}
