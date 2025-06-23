#!/bin/bash

# This script should set up a CRON job to run cron.php every 24 hours.
# You need to implement the CRON setup logic here.

# I have included the path according to my laptop using wsl for windows

if ! command -v crontab &> /dev/null
then
    echo "Error: crontab not found. This script only works on Unix-like systems or WSL."
    exit 1
fi

SCRIPT_PATH="$(dirname "$(realpath "$0")")/cron.php"
PHP_PATH=$(which php)
CRON_JOB="0 9 * * * $PHP_PATH $SCRIPT_PATH > /dev/null 2>&1"

# Add CRON job if not already present
( crontab -l 2>/dev/null | grep -v -F "$SCRIPT_PATH" ; echo "$CRON_JOB" ) | crontab -

echo "Cron job installed to run daily at 9 AM.";
