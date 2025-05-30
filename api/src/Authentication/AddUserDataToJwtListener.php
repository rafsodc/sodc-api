<?php

declare(strict_types=1);

namespace App\Authentication;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

final class AddUserDataToJwtListener
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly IriConverterInterface $iriConverter
    ) {
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        $payload = $event->getData();
        $payload['iri'] = $this->iriConverter->getIriFromResource($user);
        $payload['uuid'] = $user->getUuid();
        $event->setData($payload);
    }
} 