<?php
// api/src/Validator/Constraints/Captcha.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IPGHash extends Constraint
{
    public $message = 'IPG validation failed.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
