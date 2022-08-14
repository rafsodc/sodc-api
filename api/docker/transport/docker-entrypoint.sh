#!/bin/sh
set -e

sleep 10;
/srv/api/bin/console messenger:consume email_priority_high email_priority_normal email_priority_low >&1;