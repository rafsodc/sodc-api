<?php

declare(strict_types=1);

namespace App\Authentication;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

final class SplitJwtResponseListener
{
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        
        if (!isset($data['token'])) {
            return;
        }

        $token = $data['token'];
        $tokenParts = explode('.', $token);
        
        if (count($tokenParts) !== 3) {
            return;
        }

        // Only include header and payload in response body
        $data['token'] = $tokenParts[0] . '.' . $tokenParts[1];
        $event->setData($data);
    }
} 