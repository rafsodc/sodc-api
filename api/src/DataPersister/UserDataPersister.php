<?php
// src/DataPersister/UserDataPersister.php

namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Entity\UserSubscription;
use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use App\Repository\SubscriptionRepository;
use ApiPlatform\Api\IriConverterInterface;

class UserDataPersister implements ProcessorInterface
{
    private $entityManager;
    private $userPasswordHasher;
    private $security;
    private $subscriptionRepository;
    private $iriConverter;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        SubscriptionRepository $subscriptionRepository,
        IriConverterInterface $iriConverter
    ) {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->security = $security;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->iriConverter = $iriConverter;
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof User) {
            if ($data->getPlainPassword()) {
                $data->setPassword(
                    $this->userPasswordHasher->hashPassword($data, $data->getPlainPassword())
                );
                $data->eraseCredentials();
            }

            // Handle user subscriptions update
            if (!empty($data->getSubscriptions())) {
                // Clear current subscriptions
                foreach ($data->getUserSubscriptions() as $userSubscription) {
                    if ($userSubscription->getSubscription()->isOptout()) {
                        $this->entityManager->remove($userSubscription);
                    }
                }
                $data->getUserSubscriptions()->clear();

                // Add or remove subscriptions based on the isSubscribed flag
                foreach ($data->getSubscriptions() as $subscriptionData) {
                    $subscription = $this->iriConverter->getItemFromIri($subscriptionData['uuid']);
                    if ($subscription) {
                        if ($subscriptionData['isSubscribed']) {
                            $userSubscription = new UserSubscription();
                            $userSubscription->setSubscription($subscription);
                            $userSubscription->setOwner($data);
                            $this->entityManager->persist($userSubscription);
                            $data->addUserSubscription($userSubscription);
                        }
                    }
                }
            }

            $data->setIsMe($this->security->getUser() === $data);
            $this->entityManager->persist($data);
            $this->entityManager->flush();
        }

        return $data;
    }
}
