<?php

namespace App\Service;

final class LegacyUserExportSchema
{
    public const RECORD_SCHEMA_VERSION = 'sodc-legacy-user/v1';
    public const PREFLIGHT_SCHEMA_VERSION = 'sodc-legacy-user-preflight/v2';
    public const COMPARISON_SCHEMA_VERSION = 'sodc-legacy-user-flight-comparison/v1';
    public const MANIFEST_SCHEMA_VERSION = 'sodc-legacy-user-export-manifest/v1';

    public const FIELDS = [
        'legacyUserId',
        'oldUid',
        'email',
        'firstName',
        'lastName',
        'mobileNumber',
        'postNominals',
        'serviceNumber',
        'rank',
        'membershipStatus',
        'isShared',
        'hasSubscriptions',
        'passwordHash',
    ];

    public const REQUIRED_TARGET_FIELDS = [
        'email',
        'firstName',
        'lastName',
        'serviceNumber',
    ];

    public const MEMBERSHIP_ROLE_STATUS = [
        'ROLE_RESIGNED' => 'RESIGNED',
        'ROLE_DECEASED' => 'DECEASED',
        'ROLE_LOST' => 'LOST',
        'ROLE_SERVING' => 'REGULAR',
        'ROLE_RETIRED' => 'RETIRED',
    ];

    public const MEMBERSHIP_STATUSES = [
        'PENDING',
        'REGULAR',
        'RETIRED',
        'RESIGNED',
        'LOST',
        'DECEASED',
    ];

    public const RANKS = [
        1 => ['source' => 'Sqn Ldr', 'target' => 'Squadron Leader'],
        2 => ['source' => 'ACM', 'target' => 'Air Chief Marshal'],
        3 => ['source' => 'AM', 'target' => 'Air Marshal'],
        7 => ['source' => 'AVM', 'target' => 'Air Vice-Marshal'],
        8 => ['source' => 'Air Cdre', 'target' => 'Air Commodore'],
        9 => ['source' => 'Gp Capt', 'target' => 'Group Captain'],
        10 => ['source' => 'Wg Cdr', 'target' => 'Wing Commander'],
        11 => ['source' => 'Flt Lt', 'target' => 'Flight Lieutenant'],
        12 => ['source' => 'Mr', 'target' => 'Mr'],
        13 => ['source' => 'Dr', 'target' => 'Dr'],
        14 => ['source' => 'Fg Off', 'target' => 'Flying Officer'],
        15 => ['source' => 'Mrs', 'target' => 'Mrs'],
        16 => ['source' => 'Ms', 'target' => 'Ms'],
    ];

    private function __construct()
    {
    }
}
