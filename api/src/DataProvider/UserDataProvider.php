<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\DenormalizedIdentifiersAwareItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class UserDataProvider implements ContextAwareCollectionDataProviderInterface, DenormalizedIdentifiersAwareItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $collectionDataProvider;
    private $security;
    private $itemDataProvider;

    public function __construct(CollectionDataProviderInterface $collectionDataProvider, Security $security, ItemDataProviderInterface $itemDataProvider)
    {
        $this->collectionDataProvider = $collectionDataProvider;
        $this->security = $security;
        $this->itemDataProvider = $itemDataProvider;
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
        /** @var User|null $item */
        $item = $this->itemDataProvider->getItem($resourceClass, $id, $operationName, $context);
        if (!$item) {
            return null;
        }
        $item->setIsMe($this->security->getUser() === $item);
        return $item;
    }
}
