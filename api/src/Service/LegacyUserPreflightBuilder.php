<?php

namespace App\Service;

use App\Entity\Subscription;
use App\Entity\User;

final class LegacyUserPreflightBuilder
{
    public const SCHEMA_VERSION = 'sodc-legacy-user-preflight/v1';

    /**
     * @param iterable<User>         $users
     * @param iterable<Subscription> $subscriptions
     */
    public function build(iterable $users, iterable $subscriptions, \DateTimeImmutable $generatedAt): array
    {
        $report = $this->emptyReport($generatedAt);
        $exactEmails = [];
        $normalisedEmails = [];
        $oldUids = [];
        $roleCombinationCounts = [];
        $rankCounts = [];
        $subscriptionMembers = [];
        $subscriptionPairs = [];
        $subscriptionsPerUser = [];

        foreach ($subscriptions as $subscription) {
            $id = (string) $subscription->getUuid();
            $subscriptionMembers[$id] = [
                'id' => $id,
                'name' => $subscription->getName(),
                'members' => 0,
            ];
        }

        foreach ($users as $user) {
            ++$report['totalUsers'];
            $roles = $user->getRoles();
            if (in_array('ROLE_DELETED', $roles, true)) {
                ++$report['eligibility']['excludedRoleDeleted'];
                continue;
            }

            ++$report['eligibility']['includedUsers'];
            $membershipStatus = $this->membershipStatus($roles, $user->getIsMember());
            ++$report['membershipStatuses'][$membershipStatus];
            $oldUidCohort = null === $user->getOldUid() ? 'missing' : 'set';
            $this->countOldUidCohortAttributes($user, $roles, $membershipStatus, $report['attributesByOldUid'][$oldUidCohort]);
            $this->countEmailQuality($user->getEmail(), $report, $exactEmails, $normalisedEmails);
            $this->countValue($user->getFirstName(), $report['fieldCompleteness']['firstName']);
            $this->countValue($user->getLastName(), $report['fieldCompleteness']['lastName']);
            $this->countValue($user->getServiceNumber(), $report['fieldCompleteness']['serviceNumber']);
            $this->countValue($user->getMobileNumber(), $report['fieldCompleteness']['mobileNumber']);
            $this->countValue($user->getPhoneNumber(), $report['fieldCompleteness']['phoneNumber']);
            $this->countValue($user->getPostNominals(), $report['fieldCompleteness']['postNominals']);
            $this->countOldUid($user->getOldUid(), $report, $oldUids);
            $this->countPassword($user->getPassword(), $report);
            $this->countBoolean($user->getIsShared(), $report['sharing']);
            $this->countBoolean($user->getIsSubscribed(), $report['legacyIsSubscribed']);
            $this->countRoles($user, $report, $roleCombinationCounts);
            $this->countRank($user, $report, $rankCounts);

            $validSubscriptionIds = [];
            foreach ($user->getUserSubscriptions() as $userSubscription) {
                $subscription = $userSubscription->getSubscription();
                if (null === $subscription || null === $subscription->getUuid()) {
                    ++$report['relationshipQuality']['missingSubscription'];
                    continue;
                }

                $subscriptionId = (string) $subscription->getUuid();
                $pair = (string) $user->getUuid().'|'.$subscriptionId;
                if (isset($subscriptionPairs[$pair])) {
                    ++$report['relationshipQuality']['duplicateUserSubscriptionPairs'];
                    continue;
                }

                $subscriptionPairs[$pair] = true;
                $validSubscriptionIds[$subscriptionId] = true;
                if (!isset($subscriptionMembers[$subscriptionId])) {
                    ++$report['relationshipQuality']['missingFromCatalogue'];
                    $subscriptionMembers[$subscriptionId] = [
                        'id' => $subscriptionId,
                        'name' => $subscription->getName(),
                        'members' => 0,
                    ];
                }
                ++$subscriptionMembers[$subscriptionId]['members'];
                ++$report['subscriptionSummary']['relationshipCount'];
            }

            $relationshipCount = count($validSubscriptionIds);
            $subscriptionsPerUser[$relationshipCount] = ($subscriptionsPerUser[$relationshipCount] ?? 0) + 1;
            $hasSubscriptions = $relationshipCount > 0;
            ++$report['hasSubscriptions'][$hasSubscriptions ? 'true' : 'false'];
            ++$report['attributesByOldUid'][$oldUidCohort]['hasSubscriptions'][$hasSubscriptions ? 'true' : 'false'];
            $correlationKey = $this->booleanKey($user->getIsSubscribed()).'|'.($hasSubscriptions ? 'true' : 'false');
            $report['subscriptionCorrelation'][$correlationKey] = ($report['subscriptionCorrelation'][$correlationKey] ?? 0) + 1;
        }

        $report['emailQuality']['duplicateExact'] = $this->duplicateCount($exactEmails);
        $report['emailQuality']['duplicateNormalized'] = $this->duplicateCount($normalisedEmails);
        $report['identityQuality']['oldUid']['duplicate'] = $this->duplicateCount($oldUids);
        $report['subscriptionSummary']['catalogueSize'] = count($subscriptionMembers);
        $report['roleCombinations'] = $this->formatRoleCombinations($roleCombinationCounts);
        $report['ranks'] = $this->formatRanks($rankCounts);
        $report['subscriptions'] = array_values($subscriptionMembers);
        usort($report['subscriptions'], static fn (array $a, array $b): int => strcmp($a['id'], $b['id']));
        $report['subscriptionSummary']['subscriptionsPerUser']['distribution'] = $this->formatDistribution($subscriptionsPerUser);
        $report['subscriptionSummary']['subscriptionsPerUser']['minimum'] = empty($subscriptionsPerUser) ? 0 : min(array_keys($subscriptionsPerUser));
        $report['subscriptionSummary']['subscriptionsPerUser']['maximum'] = empty($subscriptionsPerUser) ? 0 : max(array_keys($subscriptionsPerUser));
        $report['subscriptionCorrelation'] = $this->formatCorrelation($report['subscriptionCorrelation']);

        return $report;
    }

    private function emptyReport(\DateTimeImmutable $generatedAt): array
    {
        $fieldCount = static fn (): array => ['null' => 0, 'blank' => 0, 'present' => 0];

        return [
            'schemaVersion' => self::SCHEMA_VERSION,
            'generatedAt' => $generatedAt->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z'),
            'totalUsers' => 0,
            'eligibility' => ['excludedRoleDeleted' => 0, 'includedUsers' => 0],
            'membershipStatuses' => ['active' => 0, 'pending' => 0, 'resigned' => 0, 'deceased' => 0, 'lost' => 0],
            'emailQuality' => ['missing' => 0, 'blank' => 0, 'invalid' => 0, 'leadingOrTrailingWhitespace' => 0, 'duplicateExact' => 0, 'duplicateNormalized' => 0],
            'identityQuality' => ['oldUid' => ['missing' => 0, 'present' => 0, 'duplicate' => 0]],
            'attributesByOldUid' => [
                'set' => $this->emptyOldUidCohort(),
                'missing' => $this->emptyOldUidCohort(),
            ],
            'fieldCompleteness' => [
                'firstName' => $fieldCount(), 'lastName' => $fieldCount(), 'serviceNumber' => $fieldCount(),
                'mobileNumber' => $fieldCount(), 'phoneNumber' => $fieldCount(), 'postNominals' => $fieldCount(),
            ],
            'passwords' => ['present' => 0, 'blank' => 0, 'supported' => 0, 'unsupportedOrMalformed' => 0, 'algorithms' => ['bcrypt' => 0, 'argon2i' => 0, 'argon2id' => 0]],
            'roles' => ['empty' => 0, 'counts' => []],
            'roleCombinations' => [],
            'membershipConsistency' => ['memberFlagAndRole' => 0, 'memberFlagWithoutRole' => 0, 'memberRoleWithoutFlag' => 0, 'neither' => 0, 'nullFlag' => 0],
            'ranks' => [],
            'rankQuality' => ['missing' => 0],
            'sharing' => ['true' => 0, 'false' => 0, 'null' => 0],
            'legacyIsSubscribed' => ['true' => 0, 'false' => 0, 'null' => 0],
            'hasSubscriptions' => ['true' => 0, 'false' => 0],
            'subscriptionSummary' => ['catalogueSize' => 0, 'relationshipCount' => 0, 'subscriptionsPerUser' => ['minimum' => 0, 'maximum' => 0, 'distribution' => []]],
            'subscriptionCorrelation' => [],
            'relationshipQuality' => ['missingSubscription' => 0, 'missingFromCatalogue' => 0, 'duplicateUserSubscriptionPairs' => 0],
            'subscriptions' => [],
        ];
    }

    private function countEmailQuality(?string $email, array &$report, array &$exact, array &$normalised): void
    {
        if (null === $email) {
            ++$report['emailQuality']['missing'];
            return;
        }
        if ('' === $email) {
            ++$report['emailQuality']['blank'];
            return;
        }
        if ($email !== trim($email)) {
            ++$report['emailQuality']['leadingOrTrailingWhitespace'];
        }
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ++$report['emailQuality']['invalid'];
        }
        $exact[$email] = ($exact[$email] ?? 0) + 1;
        $key = strtolower(trim($email));
        $normalised[$key] = ($normalised[$key] ?? 0) + 1;
    }

    private function countValue(?string $value, array &$counts): void
    {
        ++$counts[null === $value ? 'null' : ('' === $value ? 'blank' : 'present')];
    }

    private function emptyOldUidCohort(): array
    {
        $valueCounts = static fn (): array => ['null' => 0, 'blank' => 0, 'present' => 0];

        return [
            'users' => 0,
            'attributes' => [
                'email' => $valueCounts(),
                'firstName' => $valueCounts(),
                'lastName' => $valueCounts(),
                'mobileNumber' => $valueCounts(),
                'phoneNumber' => $valueCounts(),
                'postNominals' => $valueCounts(),
                'serviceNumber' => $valueCounts(),
                'rank' => ['missing' => 0, 'present' => 0],
                'roles' => ['empty' => 0, 'present' => 0],
                'passwordHash' => ['blank' => 0, 'present' => 0],
                'isShared' => ['true' => 0, 'false' => 0, 'null' => 0],
            ],
            'membershipStatuses' => ['active' => 0, 'pending' => 0, 'resigned' => 0, 'deceased' => 0, 'lost' => 0],
            'hasSubscriptions' => ['true' => 0, 'false' => 0],
        ];
    }

    private function countOldUidCohortAttributes(User $user, array $roles, string $membershipStatus, array &$cohort): void
    {
        ++$cohort['users'];
        $this->countValue($user->getEmail(), $cohort['attributes']['email']);
        $this->countValue($user->getFirstName(), $cohort['attributes']['firstName']);
        $this->countValue($user->getLastName(), $cohort['attributes']['lastName']);
        $this->countValue($user->getMobileNumber(), $cohort['attributes']['mobileNumber']);
        $this->countValue($user->getPhoneNumber(), $cohort['attributes']['phoneNumber']);
        $this->countValue($user->getPostNominals(), $cohort['attributes']['postNominals']);
        $this->countValue($user->getServiceNumber(), $cohort['attributes']['serviceNumber']);
        ++$cohort['attributes']['rank'][null === $user->getRank() ? 'missing' : 'present'];
        ++$cohort['attributes']['roles'][empty($roles) ? 'empty' : 'present'];
        ++$cohort['attributes']['passwordHash']['' === $user->getPassword() ? 'blank' : 'present'];
        $this->countBoolean($user->getIsShared(), $cohort['attributes']['isShared']);
        ++$cohort['membershipStatuses'][$membershipStatus];
    }

    private function countOldUid(?int $oldUid, array &$report, array &$oldUids): void
    {
        if (null === $oldUid) {
            ++$report['identityQuality']['oldUid']['missing'];
            return;
        }
        ++$report['identityQuality']['oldUid']['present'];
        $oldUids[$oldUid] = ($oldUids[$oldUid] ?? 0) + 1;
    }

    private function countPassword(string $password, array &$report): void
    {
        if ('' === $password) {
            ++$report['passwords']['blank'];
            return;
        }
        ++$report['passwords']['present'];
        $info = password_get_info($password);
        $algorithm = $info['algoName'] ?? 'unknown';
        if (isset($report['passwords']['algorithms'][$algorithm])) {
            ++$report['passwords']['algorithms'][$algorithm];
            ++$report['passwords']['supported'];
        } else {
            ++$report['passwords']['unsupportedOrMalformed'];
        }
    }

    private function countBoolean(?bool $value, array &$counts): void
    {
        ++$counts[$this->booleanKey($value)];
    }

    private function booleanKey(?bool $value): string
    {
        return null === $value ? 'null' : ($value ? 'true' : 'false');
    }

    private function membershipStatus(array $roles, ?bool $isMember): string
    {
        foreach ([
            'ROLE_RESIGNED' => 'resigned',
            'ROLE_DECEASED' => 'deceased',
            'ROLE_LOST' => 'lost',
        ] as $role => $status) {
            if (in_array($role, $roles, true)) {
                return $status;
            }
        }

        return true === $isMember ? 'active' : 'pending';
    }

    private function countRoles(User $user, array &$report, array &$combinations): void
    {
        $roles = $user->getRoles();
        $unique = array_values(array_unique($roles));
        sort($unique, SORT_STRING);
        if (empty($unique)) {
            ++$report['roles']['empty'];
        }
        foreach ($unique as $role) {
            $report['roles']['counts'][$role] = ($report['roles']['counts'][$role] ?? 0) + 1;
        }
        ksort($report['roles']['counts'], SORT_STRING);
        $memberRole = in_array('ROLE_MEMBER', $unique, true);
        $memberFlag = $user->getIsMember();
        if (null === $memberFlag) {
            ++$report['membershipConsistency']['nullFlag'];
        } elseif ($memberFlag && $memberRole) {
            ++$report['membershipConsistency']['memberFlagAndRole'];
        } elseif ($memberFlag) {
            ++$report['membershipConsistency']['memberFlagWithoutRole'];
        } elseif ($memberRole) {
            ++$report['membershipConsistency']['memberRoleWithoutFlag'];
        } else {
            ++$report['membershipConsistency']['neither'];
        }
        $key = json_encode([$unique, $memberFlag], JSON_THROW_ON_ERROR);
        $combinations[$key] = ($combinations[$key] ?? 0) + 1;
    }

    private function countRank(User $user, array &$report, array &$ranks): void
    {
        $rank = $user->getRank();
        if (null === $rank) {
            ++$report['rankQuality']['missing'];
            return;
        }
        $key = $rank->getId().'|'.$rank->getRank();
        $ranks[$key] = ['id' => $rank->getId(), 'value' => $rank->getRank(), 'count' => ($ranks[$key]['count'] ?? 0) + 1];
    }

    private function duplicateCount(array $counts): int
    {
        return array_sum(array_map(static fn (int $count): int => max(0, $count - 1), $counts));
    }

    private function formatRoleCombinations(array $counts): array
    {
        ksort($counts, SORT_STRING);
        $result = [];
        foreach ($counts as $key => $count) {
            [$roles, $isMember] = json_decode($key, true, 512, JSON_THROW_ON_ERROR);
            $result[] = ['roles' => $roles, 'isMember' => $isMember, 'count' => $count];
        }
        return $result;
    }

    private function formatRanks(array $ranks): array
    {
        $result = array_values($ranks);
        usort($result, static fn (array $a, array $b): int => [$a['id'], $a['value']] <=> [$b['id'], $b['value']]);
        return $result;
    }

    private function formatDistribution(array $counts): array
    {
        ksort($counts, SORT_NUMERIC);
        $result = [];
        foreach ($counts as $count => $users) {
            $result[] = ['count' => (int) $count, 'users' => $users];
        }
        return $result;
    }

    private function formatCorrelation(array $counts): array
    {
        $result = [];
        foreach (['true', 'false', 'null'] as $flag) {
            foreach (['true', 'false'] as $hasRelationships) {
                $result[] = [
                    'isSubscribed' => 'null' === $flag ? null : 'true' === $flag,
                    'hasSubscriptionRelationships' => 'true' === $hasRelationships,
                    'users' => $counts[$flag.'|'.$hasRelationships] ?? 0,
                ];
            }
        }
        return $result;
    }
}
