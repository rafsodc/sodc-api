<?php

/*
 * This file is part of the CoopTilleulsForgotPasswordBundle package.
 *
 * (c) Vincent Chalamon <vincent@les-tilleuls.coop>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\EventListener;

use CoopTilleuls\ForgotPasswordBundle\Exception\InvalidJsonHttpException;
use CoopTilleuls\ForgotPasswordBundle\Exception\MissingFieldHttpException;
use CoopTilleuls\ForgotPasswordBundle\Exception\NoParameterException;
use CoopTilleuls\ForgotPasswordBundle\Exception\UnauthorizedFieldException;
use CoopTilleuls\ForgotPasswordBundle\Manager\PasswordTokenManager;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\PasswordToken;
use App\Exception\InvalidCaptchaHttpException;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
final class PasswordRequestEventListener
{
    private $validator;

    /**
     * @param string $userPasswordField
     */
    public function __construct(ValidatorInterface $validator) {
        $this->validator = $validator;
    }

    public function decodeRequest(KernelEvent $event): void
    {
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');
        
        if (!$event->isMasterRequest() || 'coop_tilleuls_forgot_password.reset' !== $routeName) {
          return;
        };

        $content = $request->getContent();
        $data = json_decode($content, true);
        $captcha = isset($data['captcha']) ? $data['captcha'] : '';

        $passwordToken = new PasswordToken();
        $passwordToken->setCaptcha($captcha);

        $errors = $this->validator->validate($passwordToken);
        if($errors->count() > 0) {
          throw new InvalidCaptchaHttpException();
        }
    }
}
