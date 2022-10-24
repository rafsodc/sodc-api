<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\EventOutput;
use App\Dto\MediaObjectOutput;
use App\Dto\UserOutput;
use App\Entity\Event;
use App\Entity\MediaObject;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;
use DateTime;
use DateTimeZone;
use Vich\UploaderBundle\Storage\StorageInterface;

class MediaObjectOutputDataTransformer implements DataTransformerInterface
{
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param Transaction $transaction
     */
    public function transform($object, string $to, array $context = [])
    {
        $output = new MediaObjectOutput();
        $output->contentUrl = $this->storage->resolveUri($object, 'file');

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $data instanceof MediaObject && $to === MediaObjectOutput::class;
    }
}
