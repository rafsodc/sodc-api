<?php

declare(strict_types=1);

namespace App\Authentication;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

/**
 * Class LogoutHandler
 * This handler runs on logout.  It removes refresh token cookie of the current user.
 * @see https://gist.github.com/Arakmar/91674347dea1a763fec043d56a5855c6
 */
final class LogoutHandler implements LogoutHandlerInterface
{
    /**
     * @var RefreshTokenManagerInterface
     */
    private $refreshTokenManager;

    public function __construct(RefreshTokenManagerInterface $refreshTokenManager)
    {
        $this->refreshTokenManager = $refreshTokenManager;
    }

    public function logout(Request $request, Response $response, TokenInterface $token): void
    {
        $authenticatedUser = $token->getUser();

        if (null === $authenticatedUser) {
            return;
        }

        $securityCookieSecret = $request->cookies->get(AuthenticationService::SECURITY_COOKIE_NAME);

        if ($securityCookieSecret) {
            $refreshToken = $this->refreshTokenManager->get($securityCookieSecret);
            if ($refreshToken) {
                $this->refreshTokenManager->delete($refreshToken);
            }

            $response->headers->clearCookie(AuthenticationService::SECURITY_COOKIE_NAME);
        }

        $response->setStatusCode(200);
    }
}
