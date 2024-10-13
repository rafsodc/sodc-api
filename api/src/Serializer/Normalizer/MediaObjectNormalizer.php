<?php

namespace App\Serializer\Normalizer;

use App\Entity\MediaObject;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class MediaObjectNormalizer implements ContextAwareNormalizerInterface, CacheableSupportsMethodInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private $router;
    private const ALREADY_CALLED = 'MEDIA_OBJECT_NORMALIZER_ALREADY_CALLED';

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        // Set the context to prevent recursion
        $context[self::ALREADY_CALLED] = true;
        
        // Normalize the base data
        $data = $this->normalizer->normalize($object, $format, $context);

        // Add the generated contentUrl field
        $data['contentUrl'] = $this->router->generate('app_media', [
            'id' => $object->getId(),
            'file' => $object->getFilePath()
        ]);

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        // Prevent recursion by checking if normalization was already called
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        // Make sure the normalizer is properly initialized
        if (!$this->normalizer) {
            throw new \LogicException('The normalizer has not been set.');
        }
        
        return $data instanceof MediaObject;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return false;
    }
}