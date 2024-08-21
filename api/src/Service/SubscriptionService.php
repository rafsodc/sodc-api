<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Subscription;
use App\Entity\UserSubscription;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setDefaultSubscriptions(User $user): void
    {
        // Fetch all subscriptions
        $subscriptions = $this->entityManager->getRepository(Subscription::class)->findAll();

        foreach ($subscriptions as $subscription) {
            // Check if the user has any of the roles required for this subscription
            if ($this->hasMatchingRole($user->getRoles(), $subscription->getRoles())) {
                $userSubscription = new UserSubscription();
                $userSubscription->setOwner($user);
                $userSubscription->setSubscription($subscription);

                $this->entityManager->persist($userSubscription);
            }
        }

        $this->entityManager->flush();
    }

    private function hasMatchingRole(array $userRoles, array $subscriptionRoles): bool
    {
        // Return true if any of the user's roles match the subscription's roles
        return !empty(array_intersect($userRoles, $subscriptionRoles));
    }
}
