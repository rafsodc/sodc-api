<?php

namespace App\ApiPlatform;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;

/**
 * Provides dynamic groups that can be viewed in API documentation.
 *
 * The class provides dynamic groups based on entity class, whether it's an item or collection, and whether it's a normalisation (read)
 * or denormalisation (write) action.
 *
 * @see https://symfonycasts.com/screencast/api-platform-security/resource-metadata-factory
 *
 * @package App\ApiPlatform
 */
class AutoGroupResourceMetadataFactory implements ResourceMetadataFactoryInterface
{
    /**
     * @var ResourceMetadataFactoryInterface
     */
    private $decorated;
    /**
     * Contains any previously cached resourceMetadata values
     * @var array
     */
    private $resourceMetadata = [];

    /**
     * AutoGroupResourceMetadataFactory constructor.
     * @param ResourceMetadataFactoryInterface $decorated
     */
    public function __construct(ResourceMetadataFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * Returns ResourceMetadata with created groups.
     *
     * @param string $resourceClass
     * @return ResourceMetadata
     * @throws \ApiPlatform\Core\Exception\ResourceClassNotFoundException
     */
    public function create(string $resourceClass): ResourceMetadata
    {
        // Cache the results for future uses on page load
        if(!isset($this->resourceMetadata[$resourceClass])) {
            $resourceMetadata = $this->decorated->create($resourceClass);

            $itemOperations = $resourceMetadata->getItemOperations();
            $resourceMetadata = $resourceMetadata->withItemOperations(
                $this->updateContextOnOperations($itemOperations, $resourceMetadata->getShortName(), true)
            );
            $collectionOperations = $resourceMetadata->getCollectionOperations();
            $resourceMetadata = $resourceMetadata->withCollectionOperations(
                $this->updateContextOnOperations($collectionOperations, $resourceMetadata->getShortName(), false)
            );

            $this->resourceMetadata[$resourceClass] = $resourceMetadata;
        }

        return $this->resourceMetadata[$resourceClass];
    }

    /**
     * Sets 'groups' element to 'normalization_context' and 'denormalization_context' if they do not exist, and merges with created groups
     *
     * @param array $operations
     * @param string $shortName
     * @param bool $isItem
     * @return array
     */
    private function updateContextOnOperations(array $operations, string $shortName, bool $isItem)
    {
        foreach ($operations as $operationName => $operationOptions) {
            $operationOptions['normalization_context'] = $operationOptions['normalization_context'] ?? [];
            $operationOptions['normalization_context']['groups'] = $operationOptions['normalization_context']['groups'] ?? [];
            $operationOptions['normalization_context']['groups'] = array_unique(array_merge(
                $operationOptions['normalization_context']['groups'],
                $this->getDefaultGroups($shortName, true, $isItem, $operationName)
            ));
            $operationOptions['denormalization_context'] = $operationOptions['denormalization_context'] ?? [];
            $operationOptions['denormalization_context']['groups'] = $operationOptions['denormalization_context']['groups'] ?? [];
            $operationOptions['denormalization_context']['groups'] = array_unique(array_merge(
                $operationOptions['denormalization_context']['groups'],
                $this->getDefaultGroups($shortName, false, $isItem, $operationName)
            ));
            $operations[$operationName] = $operationOptions;
        }
        return $operations;
    }

    /**
     * Creates default groups based on entity class, whether it's an item or collection, and whether it's a normalisation (read)
     * or denormalisation (write) action.
     *
     * @param string $shortName
     * @param bool $normalization
     * @param bool $isItem
     * @param string $operationName
     * @return array
     */
    private function getDefaultGroups(string $shortName, bool $normalization, bool $isItem, string $operationName)
    {
        $shortName = strtolower($shortName);
        $readOrWrite = $normalization ? 'read' : 'write';
        $itemOrCollection = $isItem ? 'item' : 'collection';
        return [
            // {shortName}:{read/write}
            // e.g. user:read
            sprintf('%s:%s', $shortName, $readOrWrite),
            // {shortName}:{operationName}
            // e.g. user:get
            sprintf('%s:%s', $shortName, $operationName),
            // {shortName}:{item/collection}:{read/write}
            // e.g. user:collection:read
            sprintf('%s:%s:%s', $shortName, $itemOrCollection, $readOrWrite),
            // {shortName}:{item/collection}:{operationName}
            // e.g. user:collection:get
            sprintf('%s:%s:%s', $shortName, $itemOrCollection, $operationName),
        ];
    }
}
