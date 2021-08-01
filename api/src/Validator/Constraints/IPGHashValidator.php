<?php
// api/src/Validator/Constraints/CaptchaValidator.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @Annotation
 */
final class IPGHashValidator extends ConstraintValidator
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function validate($protocol, Constraint $constraint): void
    {
        $myHash = $this->createHash($protocol->getTotal(), $protocol->getCurrency(), $protocol->getTxndate(), $protocol->getApprovalCode());

        //$errors = $this->validator->validate($passwordToken);
        //dd($myHash . ':' . $protocol->getNotificationHash());
        //$request = Request::createFromGlobals();

        // $recaptcha = new \ReCaptcha\ReCaptcha($this->params->get('google_recaptcha_secret_key'));
        // $resp = $recaptcha//->setExpectedHostname($request->getHost())
        //     ->verify($value, $request->getClientIp());

        if ($myHash !== $protocol->getNotificationHash()) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    private function createHash($amount, $currency, $dateTime, $approvalCode) {
        $stringToHash = number_format($amount, 2) . $this->params->get('ipg_secret_key') . $currency . $dateTime->format("Y:m:d-H:i:s") . $this->params->get('ipg_store_id') . $approvalCode;
        $ascii = bin2hex($stringToHash);
        return hash('sha256',$ascii);
    }
}
