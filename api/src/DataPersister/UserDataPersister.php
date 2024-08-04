<?php
// src/DataPersister/UserDataPersister.php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use App\Entity\UserSubscription;
use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use App\Repository\SubscriptionRepository;
use ApiPlatform\Core\Api\IriConverterInterface;

class UserDataPersister implements DataPersisterInterface
{
    private $decoratedDataPersister;
    private $userPasswordEncoder;
    private $security;
    private $entityManager;
    private $subscriptionRepository;
    private $iriConverter;

    public function __construct(
        DataPersisterInterface $decoratedDataPersister,
        UserPasswordEncoderInterface $userPasswordEncoder,
        Security $security,
        EntityManagerInterface $entityManager,
        SubscriptionRepository $subscriptionRepository,
        IriConverterInterface $iriConverter
    ) {
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->iriConverter = $iriConverter;
    }

    public function supports($data): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     */
    public function persist($data)
    {
        if ($data->getPlainPassword()) {
            $data->setPassword(
                $this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword())
            );
            $data->eraseCredentials();
        }

        // Handle user subscriptions update
        if (!empty($data->getSubscriptions())) {
            // Clear current subscriptions
            foreach ($data->getUserSubscriptions() as $userSubscription) {
                $this->entityManager->remove($userSubscription);
            }
            $data->getUserSubscriptions()->clear();

            // Add new subscriptions
            foreach ($data->getSubscriptions() as $subscriptionIri) {
                $subscription = $this->iriConverter->getItemFromIri($subscriptionIri);
                if ($subscription) {
                    $userSubscription = new UserSubscription();
                    $userSubscription->setSubscription($subscription);
                    $userSubscription->setOwner($data);
                    $this->entityManager->persist($userSubscription);
                    $data->addUserSubscription($userSubscription);
                }
            }
        }

        $data->setIsMe($this->security->getUser() === $data);
        $this->decoratedDataPersister->persist($data);
        $this->entityManager->flush();

        // Ensure the response includes the transformed subscriptions
        $this->transformUserSubscriptions($data);
        return $data;
    }

    public function remove($data)
    {
        $this->decoratedDataPersister->remove($data);
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
            $subscriptions[] = [
                'uuid' => $subscription->getUuid(),
                'name' => $subscription->getName(),
                'isSubscribed' => in_array($subscription->getUuid()->toString(), $subscribedSubscriptionIds)
            ];
        }

        $user->setSubscriptions($subscriptions);
    }
}
