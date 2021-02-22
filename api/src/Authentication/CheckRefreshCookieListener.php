<?php

declare(strict_types=1);

namespace App\Authentication;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class CheckRefreshCookieListener
 * Don't allow requests without a valid refresh token. Double security.
 * @see https://gist.github.com/Arakmar/91674347dea1a763fec043d56a5855c6
 */
class CheckRefreshCookieListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var RefreshTokenManagerInterface
     */
    private $refreshTokenManager;

    public function __construct(RequestStack $requestStack, RefreshTokenManagerInterface $refreshTokenManager)
    {
        $this->requestStack = $requestStack;
        $this->refreshTokenManager = $refreshTokenManager;
    }

    public function onJWTDecoded(JWTDecodedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $securityCookieSecret = $request->cookies->get(AuthenticationService::SECURITY_COOKIE_NAME);

        if ($securityCookieSecret) {
            $refreshToken = $this->refreshTokenManager->get($securityCookieSecret);
            // Our user has a valid refresh token, let the authentication process to continue
            if ($refreshToken && $refreshToken->isValid()) {
                return;
            }
        }

        $event->markAsInvalid();
    }
}
