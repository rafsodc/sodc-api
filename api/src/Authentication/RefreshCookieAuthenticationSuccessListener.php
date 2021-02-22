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
    /**
     * @var AuthenticationService
     */
    private $authenticationService;
    private $refreshTokenparameterName;

    public function __construct(AuthenticationService $authenticationService, $refreshTokenparameterName)
    {
        $this->authenticationService = $authenticationService;
        $this->refreshTokenparameterName = $refreshTokenparameterName;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();

        if (array_key_exists($this->refreshTokenparameterName, $data)) {
            $refreshTokenString = $data[$this->refreshTokenparameterName];
            /** @var Cookie $securityCookie */
            $securityCookie = $this->authenticationService->createSecurityCookie($refreshTokenString);
            if ($securityCookie) {
                $event->getResponse()->headers->setCookie($securityCookie);
            }
            // Don't add the resfresh token in the response, the frontend doesn't have to known about it
            unset($data[$this->refreshTokenparameterName]);
        }
        $event->setData($data);
    }
}
