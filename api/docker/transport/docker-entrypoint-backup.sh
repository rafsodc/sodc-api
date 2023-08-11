#!/bin/sh
set -e

sleep 10;
if [ "$APP_ENV" = 'prod' ]; then
    php bin/console secrets:decrypt-to-local --force --env=prod
    composer dump-env prod
fi
/srv/api/bin/console messenger:consume backup >&1;