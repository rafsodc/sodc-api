<?php
// api/src/Validator/Constraints/Captcha.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute]
class Captcha extends Constraint
{
    public $message = 'Captcha validation failed.';
}
