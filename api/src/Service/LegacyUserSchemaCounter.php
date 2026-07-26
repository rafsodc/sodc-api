<?php

namespace App\Service;

use Ramsey\Uuid\Uuid;

final class LegacyUserSchemaCounter
{
    public function emptyCounts(): array
    {
        $valueCounts = static fn (): array => ['null' => 0, 'blank' => 0, 'present' => 0];
        $rankValues = [];
        foreach (LegacyUserExportSchema::RANKS as $rank) {
            $rankValues[$rank['target']] = 0;
        }
        $membershipStatuses = [];
        foreach (LegacyUserExportSchema::MEMBERSHIP_STATUSES as $status) {
            $membershipStatuses[$status] = 0;
        }
        $missingRequiredFields = [];
        foreach (LegacyUserExportSchema::REQUIRED_TARGET_FIELDS as $field) {
            $missingRequiredFields[$field] = 0;
        }

        return [
            'recordCount' => 0,
            'requiredFields' => [
                'usersComplete' => 0,
                'usersMissingAny' => 0,
                'missingByField' => $missingRequiredFields,
            ],
            'fields' => [
                'legacyUserId' => ['present' => 0],
                'oldUid' => ['null' => 0, 'present' => 0],
                'email' => $valueCounts(),
                'firstName' => $valueCounts(),
                'lastName' => $valueCounts(),
                'mobileNumber' => $valueCounts(),
                'postNominals' => $valueCounts(),
                'serviceNumber' => $valueCounts(),
                'rank' => ['null' => 0, 'values' => $rankValues],
                'membershipStatus' => $membershipStatuses,
                'isShared' => ['true' => 0, 'false' => 0, 'null' => 0],
                'hasSubscriptions' => ['true' => 0, 'false' => 0],
                'passwordHash' => $valueCounts(),
            ],
        ];
    }

    public function count(array $record, array & $counts): void
    {
        if (LegacyUserExportSchema::FIELDS !== array_keys($record)) {
            throw new \UnexpectedValueException('Export record fields do not match the approved schema.');
        }
        if (!is_string($record['legacyUserId']) || !Uuid::isValid($record['legacyUserId'])) {
            throw new \UnexpectedValueException('legacyUserId must be a UUID.');
        }
        if (null !== $record['oldUid'] && !is_int($record['oldUid'])) {
            throw new \UnexpectedValueException('oldUid must be an integer or null.');
        }

        ++$counts['recordCount'];
        ++$counts['fields']['legacyUserId']['present'];
        ++$counts['fields']['oldUid'][null === $record['oldUid'] ? 'null' : 'present'];
        foreach (['email', 'firstName', 'lastName', 'mobileNumber', 'postNominals', 'serviceNumber', 'passwordHash'] as $field) {
            $this->countValue($field, $record[$field], $counts['fields'][$field]);
        }
        $missingRequiredValue = false;
        foreach (LegacyUserExportSchema::REQUIRED_TARGET_FIELDS as $field) {
            if (null === $record[$field] || '' === trim($record[$field])) {
                ++$counts['requiredFields']['missingByField'][$field];
                $missingRequiredValue = true;
            }
        }
        ++$counts['requiredFields'][$missingRequiredValue ? 'usersMissingAny' : 'usersComplete'];
        if (null === $record['rank']) {
            ++$counts['fields']['rank']['null'];
        } elseif (is_string($record['rank']) && array_key_exists($record['rank'], $counts['fields']['rank']['values'])) {
            ++$counts['fields']['rank']['values'][$record['rank']];
        } else {
            throw new \UnexpectedValueException('rank must be an approved value or null.');
        }
        if (!is_string($record['membershipStatus']) || !array_key_exists($record['membershipStatus'], $counts['fields']['membershipStatus'])) {
            throw new \UnexpectedValueException('membershipStatus must be an approved value.');
        }
        ++$counts['fields']['membershipStatus'][$record['membershipStatus']];
        if (null !== $record['isShared'] && !is_bool($record['isShared'])) {
            throw new \UnexpectedValueException('isShared must be a boolean or null.');
        }
        ++$counts['fields']['isShared'][$this->booleanKey($record['isShared'])];
        if (!is_bool($record['hasSubscriptions'])) {
            throw new \UnexpectedValueException('hasSubscriptions must be a boolean.');
        }
        ++$counts['fields']['hasSubscriptions'][$this->booleanKey($record['hasSubscriptions'])];
    }

    private function countValue(string $field, $value, array & $counts): void
    {
        if (null !== $value && !is_string($value)) {
            throw new \UnexpectedValueException(sprintf('%s must be a string or null.', $field));
        }
        ++$counts[null === $value ? 'null' : ('' === $value ? 'blank' : 'present')];
    }

    private function booleanKey(?bool $value): string
    {
        return null === $value ? 'null' : ($value ? 'true' : 'false');
    }
}
