#!/bin/sh

if [ "$APP_ENV" = 'prod' ]; then
  composer dump-env prod
fi