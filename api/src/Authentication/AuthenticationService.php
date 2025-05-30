<?php

declare(strict_types=1);

namespace App\Authentication;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Service to handle authentication-related functionality
 */
final class AuthenticationService
{
    public const SECURITY_COOKIE_NAME = 'security';

    public function __construct(
        private readonly RefreshTokenManagerInterface $refreshTokenManager
    ) {
    }

    public function createSecurityCookie(string $refreshToken): ?Cookie
    {
        $refreshTokenEntity = $this->refreshTokenManager->get($refreshToken);

        if (!$refreshTokenEntity) {
            return null;
        }

        return new Cookie(
            self::SECURITY_COOKIE_NAME,
            $refreshTokenEntity->getRefreshToken(),
            $refreshTokenEntity->getValid(),
            null,
            null,
            false,
            true
        );
    }
}
