<?php

declare(strict_types=1);

namespace App\Authentication;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Class RefreshCookieAuthenticationSuccessListener
 * Use a cookie for the refresh token and don't include it in the response body.
 * @see https://gist.github.com/Arakmar/91674347dea1a763fec043d56a5855c6
 */
class RefreshCookieAuthenticationSuccessListener
{
    public function __construct(
        private readonly AuthenticationService $authenticationService,
        private readonly string $refreshTokenParameterName = 'refresh_token'
    ) {
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();

        if (isset($data[$this->refreshTokenParameterName])) {
            $refreshTokenString = $data[$this->refreshTokenParameterName];
            /** @var Cookie $securityCookie */
            $securityCookie = $this->authenticationService->createSecurityCookie($refreshTokenString);
            if ($securityCookie) {
                $event->getResponse()->headers->setCookie($securityCookie);
            }
            // Don't add the refresh token in the response, the frontend doesn't have to known about it
            //unset($data[$this->refreshTokenParameterName]);
            $event->setData($data);
        }
    }
}
