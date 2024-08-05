<?php
// src/DataProvider/UserDataProvider.php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\DenormalizedIdentifiersAwareItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use App\Entity\Subscription;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\Api\IriConverterInterface;

class UserDataProvider implements ContextAwareCollectionDataProviderInterface, DenormalizedIdentifiersAwareItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $collectionDataProvider;
    private $security;
    private $entityManager;
    private $iriConverter;

    public function __construct(CollectionDataProviderInterface $collectionDataProvider, Security $security, EntityManagerInterface $entityManager, IriConverterInterface $iriConverter)
    {
        $this->collectionDataProvider = $collectionDataProvider;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->iriConverter = $iriConverter;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        /** @var User[] $users */
        $users = $this->collectionDataProvider->getCollection($resourceClass, $operationName, $context);

        $currentUser = $this->security->getUser();
        foreach ($users as $user) {
            $user->setIsMe($currentUser === $user);
        }

        return $users;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === User::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var User|null $user */
        $user = $this->entityManager->getRepository($resourceClass)->find($id);
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
