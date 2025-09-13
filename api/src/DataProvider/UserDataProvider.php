<?php
// src/DataProvider/UserDataProvider.php

namespace App\DataProvider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Entity\Subscription;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Api\IriConverterInterface;

class UserDataProvider implements ProviderInterface
{
    private $security;
    private $entityManager;
    private $iriConverter;
    private $resourceMetadataCollectionFactory;

    public function __construct(
        Security $security,
        EntityManagerInterface $entityManager,
        IriConverterInterface $iriConverter,
        ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory
    ) {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->iriConverter = $iriConverter;
        $this->resourceMetadataCollectionFactory = $resourceMetadataCollectionFactory;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return $this->getCollection($operation, $context);
        }

        return $this->getItem($operation, $uriVariables, $context);
    }

    private function getCollection(Operation $operation, array $context = []): array
    {
        /** @var User[] $users */
        $users = $this->entityManager->getRepository(User::class)->findAll();

        $currentUser = $this->security->getUser();
        foreach ($users as $user) {
            $user->setIsMe($currentUser === $user);
        }

        return $users;
    }

    private function getItem(Operation $operation, array $uriVariables, array $context = []): ?User
    {
        /** @var User|null $user */
        $user = $this->entityManager->getRepository(User::class)->find($uriVariables['uuid']);
        if (!$user) {
            return null;
        }

        $user->setIsMe($this->security->getUser() === $user);

        $this->transformUserSubscriptions($user);

        return $user;
    }

    private function transformUserSubscriptions(User $user): void
    {
        // Fetch all subscriptions
        $subscriptionRepo = $this->entityManager->getRepository(Subscription::class);
        $allSubscriptions = $subscriptionRepo->findAll();

        // Create a map of subscriptions the user is subscribed to
        $subscribedSubscriptionIds = [];
        foreach ($user->getUserSubscriptions() as $userSubscription) {
            $subscribedSubscriptionIds[] = $userSubscription->getSubscription()->getUuid()->toString();
        }

        // Build the subscriptions array with subscription status
        $subscriptions = [];
        foreach ($allSubscriptions as $subscription) {
            if ($subscription->isOptout()) {
                $subscriptions[] = [
                    'uuid' => $this->iriConverter->getIriFromItem($subscription),
                    'name' => $subscription->getName(),
                    'isSubscribed' => in_array($subscription->getUuid()->toString(), $subscribedSubscriptionIds)
                ];
            }
        }

        $user->setSubscriptions($subscriptions);
    }
}
