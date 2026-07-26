# API

The API will be here.

Refer to the [Getting Started Guide](https://api-platform.com/docs/distribution) for more information.

## Legacy user migration export and checks

Run all export commands with read-only database credentials against the intended point-in-time snapshot.

Generate the non-PII preflight counts:

```console
php bin/console app:user:export-preflight --output=/secure/migration/legacy-user-preflight.json
```

The report counts only the canonical export fields, overall and split by whether `oldUid` is set. It also reports how many users are missing any destination-required value and the missing counts for `email`, `firstName`, `lastName`, and `serviceNumber`; null, empty, and whitespace-only values are treated as missing. It contains no user values, roles, subscription details, or password hashes.

Generate the encrypted JSONL artifact:

```console
php bin/console app:user:export-jsonl /secure/migration/legacy-users.jsonl.gpg --recipient=FULL_GPG_FINGERPRINT
```

Plaintext JSONL is streamed directly to GPG and is never written to disk. The command rejects destinations inside the repository, refuses to overwrite files, and creates an adjacent non-PII manifest containing the record schema version, record count, encrypted filename, and encrypted-file SHA-256. Transfer the encrypted artifact through the approved secure channel and provide the private key separately.

After the Firebase destination has generated its postflight report, compare it with preflight:

```console
php bin/console app:user:compare-postflight \
  /secure/migration/legacy-user-preflight.json \
  /secure/migration/firebase-postflight.json \
  --output=/secure/migration/postflight-comparison.json
```

The comparison ignores wrapper timestamps, but requires the record schema version, all overall counters, and both `oldUid` cohorts to match exactly. It exits non-zero when any canonical count differs.
