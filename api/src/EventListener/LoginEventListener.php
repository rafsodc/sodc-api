<?php

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

final class LoginEventListener
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
        
        if (!$event->isMasterRequest() || 'app_login' !== $routeName) {
          return;
        };

        $content = $request->getContent();
        $data = json_decode($content, true);

        // Resend request with lower case email
        $data['email'] = strtolower($data['email']);
        $request->initialize(
          $request->query->all(),
          $request->request->all(),
          $request->attributes->all(),
          $request->cookies->all(),
          $request->files->all(),
          $request->server->all(),
          json_encode($data)
       );
    }
}
