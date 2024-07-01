<?php

namespace App\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class UserNormalizer implements ContextAwareNormalizerInterface, CacheableSupportsMethodInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    private $security;

    private const ALREADY_CALLED = 'USER_NORMALIZER_ALREADY_CALLED';

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function normalize($object, $format = null, array $context = array())
    {   
        if ($this->userIsOwner($object)) {
            $context['groups'][] = 'owner:read';
        }

        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);

        return $data;
    }


    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        // Skip normalization if the context includes the bulk_notification:read group
        if (isset($context['groups']) && in_array('bulknotification:read', $context['groups'])) {
            return false;
        }

        if(isset($context['operation_type'])) {
            if($context['operation_type'] == 'collection') {
               return false;
            }
        }
        // avoid recursion: only call once per object
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof User;
    }

    private function userIsOwner(User $user): bool
    {
        /** @var User|null $authenticatedUser */
        $authenticatedUser = $this->security->getUser();
        if (!$authenticatedUser) {
            return false;
        }
        return $authenticatedUser->getId() === $user->getId();
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return false;
    }
}
