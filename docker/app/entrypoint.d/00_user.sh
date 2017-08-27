#!/bin/bash
set -e

if [ ! -z "${DEBUG}" ] ; then
    set -x
fi

# Change www-data's uid & guid to be the same as directory in host
# Fix cache problems
: ${WWW_DATA_TESTER_PATH:="/var/www/app"}
: ${WWW_DATA_UID:=`stat -c %u ${WWW_DATA_TESTER_PATH}`}
: ${WWW_DATA_GID:=`stat -c %g ${WWW_DATA_TESTER_PATH}`}

usermod -u ${WWW_DATA_UID} www-data || true
groupmod -g ${WWW_DATA_GID} www-data || true
