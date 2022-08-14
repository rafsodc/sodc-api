#!/bin/sh
set -e

sleep 10;
if [ "$APP_ENV" = 'prod' ]; then
    php bin/console secrets:decrypt-to-local --force --env=prod
    composer dump-env prod
fi
/srv/api/bin/console messenger:consume email_priority_high email_priority_normal email_priority_low >&1;