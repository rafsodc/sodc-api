<?php

declare(strict_types=1);

namespace App\Authentication;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Class AuthenticationService
 * @see https://gist.github.com/Arakmar/91674347dea1a763fec043d56a5855c6
 */
final class AuthenticationService
{
    public const SECURITY_COOKIE_NAME = 'security';

    /** @var RefreshTokenManagerInterface */
    private $refreshTokenManager;

    public function __construct(RefreshTokenManagerInterface $refreshTokenManager)
    {
        $this->refreshTokenManager = $refreshTokenManager;
    }

    public function createSecurityCookie($refreshToken): ?Cookie
    {
        $refreshToken = $this->refreshTokenManager->get($refreshToken);

        if ($refreshToken) {
            return new Cookie(
                self::SECURITY_COOKIE_NAME,
                $refreshToken->getRefreshToken(),
                $refreshToken->getValid(),
                null,
                null,
                false,
                true
            );
        } else {
            return null;
        }
    }
}
