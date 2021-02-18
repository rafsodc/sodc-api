<?php
// api/src/Validator/Constraints/CaptchaValidator.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @Annotation
 */
final class CaptchaValidator extends ConstraintValidator
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function validate($value, Constraint $constraint): void
    {
        $request = Request::createFromGlobals();

        $recaptcha = new \ReCaptcha\ReCaptcha($this->params->get('google_recaptcha_secret_key'));
        $resp = $recaptcha//->setExpectedHostname($request->getHost())
            ->verify($value, $request->getClientIp());

        if (!$resp->isSuccess()) {
            $msg = implode("; ", $resp->getErrorCodes());
            //$msg = $request->getHost();
            $this->context->buildViolation($value)->addViolation();
        }
    }
}
