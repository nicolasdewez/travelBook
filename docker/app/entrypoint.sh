#!/bin/bash
set -e

if [ ! -z "${DEBUG}" ] ; then
    set -x
fi

# Scripts to execute at runtime
for script in "/usr/local/bin/entrypoint.d"/*; do
    if [[ -x "$script" ]]; then
        ${script}
    fi
done

sh /usr/local/bin/docker-php-entrypoint $1
