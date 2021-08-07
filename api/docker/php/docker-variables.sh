#!/bin/sh

if [ "$APP_ENV" = 'prod' ]; then
  php bin/console secrets:decrypt-to-local --force --env=prod
  composer dump-env prod
fi