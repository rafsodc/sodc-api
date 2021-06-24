<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\DenormalizedIdentifiersAwareItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Event;
use Symfony\Component\Security\Core\Security;

class EventDataProvider implements ContextAwareCollectionDataProviderInterface, DenormalizedIdentifiersAwareItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $collectionDataProvider;
    private $security;
    private $itemDataProvider;

    public function __construct(CollectionDataProviderInterface $collectionDataProvider, ItemDataProviderInterface $itemDataProvider)
    {
        $this->collectionDataProvider = $collectionDataProvider;
        $this->itemDataProvider = $itemDataProvider;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        /** @var Event[] $collection */
        $collection = $this->collectionDataProvider->getCollection($resourceClass, $operationName, $context);

        foreach ($collection as $item) {
            $item->setIsBookingOpen($this->getIsOpen($item));
        }

        return $collection;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Event::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var Event|null $item */
        $item = $this->itemDataProvider->getItem($resourceClass, $id, $operationName, $context);
        if (!$item) {
            return null;
        }
        $item->setIsBookingOpen($this->getIsOpen($item));
        return $item;
    }

    public function getIsOpen($item): bool {
        $now = new \DateTime();
        return ($now >= $item->getBookingOpen()) && ($now <= $item->getBookingClose());
    }
}
