<?php
// api/src/Validator/Constraints/Captcha.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Captcha extends Constraint
{
    public $message = 'Captcha validation failed.';
}
