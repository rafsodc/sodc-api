<?php

declare(strict_types=1);

namespace App\Authentication;

use Gesdinet\JWTRefreshTokenBundle\Security\Authenticator\RefreshTokenAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

/**
 * Class CookieRefreshTokenAuthenticator
 * Use the refresh token in the cookie instead of the json body.
 * @see https://gist.github.com/Arakmar/91674347dea1a763fec043d56a5855c6
 */
class CookieRefreshTokenAuthenticator extends RefreshTokenAuthenticator
{
    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    public function __construct(UserCheckerInterface $userChecker, $tokenParameterName, AuthenticationService $authenticationService)
    {
        parent::__construct($userChecker, $tokenParameterName);
        $this->authenticationService = $authenticationService;
    }

    public function supports(Request $request)
    {
        $securityCookieSecret = $request->cookies->get(AuthenticationService::SECURITY_COOKIE_NAME);

        return $securityCookieSecret !== null;
    }

    public function getCredentials(Request $request)
    {
        $securityCookieSecret = $request->cookies->get(AuthenticationService::SECURITY_COOKIE_NAME);

        return [
            'token' => $securityCookieSecret,
        ];
    }
}
