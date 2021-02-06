<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\UserOutput;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class UserOutputDataTransformer implements DataTransformerInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param User $user
     */
    public function transform($user, string $to, array $context = [])
    {
        $output = new UserOutput();
        $output->username = $user->getUsername();
        $output->email = $user->getEmail();
        $output->phoneNumber = $user->getPhoneNumber();
        $output->isMe = $this->isMe($user);
        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $data instanceof User && $to === UserOutput::class;
    }

    private function isMe(User $user): bool
    {
        /** @var User|null $authenticatedUser */
        $authenticatedUser = $this->security->getUser();
        if (!$authenticatedUser) {
            return false;
        }
        return $authenticatedUser->getId() === $user->getId();
    }
}
