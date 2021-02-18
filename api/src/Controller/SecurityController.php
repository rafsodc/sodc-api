<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\Api\IriConverterInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityController for /login.  This runs when a user has been correctly logged in via json_login.
 * This also gets called when the content-type is the wrong format, but is caught by checking IS_AUTHENTICATED_FULLY.
 * Returns a Location header to user IRI if the user is logged in.
 *
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * Runs after login event.
     *
     * @Route("/login", name="app_login", methods={"POST"})
     */
    public function login(IriConverterInterface $iriConverter)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'error' => 'Invalid login request: check that the Content-Type header is "application/json".'
            ], 400);
        }

        return new Response(null, 204, [
            'Location' => $iriConverter->getIriFromItem($this->getUser())
        ]);
    }

    /**
     * Should not run after logout event, so return error if it does.
     *
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('should not be reached');
    }
}
