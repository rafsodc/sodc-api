<?php

namespace App\Service;

use App\Entity\User;

final class LegacyUserRecordTransformer
{
    public function transform(User $user): ?array
    {
        $roles = $user->getRoles();
        if (in_array('ROLE_DELETED', $roles, true)) {
            return null;
        }

        return [
            'legacyUserId' => (string) $user->getUuid(),
            'oldUid' => $user->getOldUid(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'mobileNumber' => $user->getMobileNumber() ?? $user->getPhoneNumber(),
            'postNominals' => $user->getPostNominals(),
            'serviceNumber' => $user->getServiceNumber(),
            'rank' => $this->rank($user),
            'membershipStatus' => $this->membershipStatus($roles, $user->getIsMember()),
            'isShared' => $user->getIsShared(),
            'hasSubscriptions' => $this->hasSubscriptions($user),
            'passwordHash' => $user->getPassword(),
        ];
    }

    private function rank(User $user): ?string
    {
        $rank = $user->getRank();
        if (null === $rank) {
            return null;
        }

        $mapping = LegacyUserExportSchema::RANKS[$rank->getId()] ?? null;
        if (null === $mapping || $mapping['source'] !== $rank->getRank()) {
            throw new \UnexpectedValueException('Legacy rank does not have an approved export mapping.');
        }

        return $mapping['target'];
    }

    private function membershipStatus(array $roles, ?bool $isMember): string
    {
        foreach (LegacyUserExportSchema::MEMBERSHIP_ROLE_STATUS as $role => $status) {
            if (in_array($role, $roles, true)) {
                return $status;
            }
        }

        return true === $isMember ? 'REGULAR' : 'PENDING';
    }

    private function hasSubscriptions(User $user): bool
    {
        foreach ($user->getUserSubscriptions() as $userSubscription) {
            $subscription = $userSubscription->getSubscription();
            if (null !== $subscription && null !== $subscription->getUuid()) {
                return true;
            }
        }

        return false;
    }
}
