# API

The API will be here.

Refer to the [Getting Started Guide](https://api-platform.com/docs/distribution) for more information.

## Legacy user migration preflight

Run the read-only, non-PII aggregate report with:

```console
php bin/console app:user:export-preflight > legacy-user-preflight.json
```

Run it with read-only database credentials against the intended point-in-time snapshot. The report contains counts plus approved role, rank, and subscription catalogue values; it does not contain user UUIDs, emails, names, phone numbers, service numbers, or password hashes. Review and transfer the report according to the migration procedure even though it contains no direct user records.
