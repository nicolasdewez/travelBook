#!/bin/bash
set -e

if [ ! -z "${DEBUG}" ] ; then
    set -x
fi

rm -f /usr/local/etc/php/conf.d/xdebug.ini

if [ ! -z "${XDEBUG_ENABLED}" ]; then
    if [ ${XDEBUG_ENABLED} = "1" ] ; then
        cp /usr/local/etc/php/xdebug.ini.sample /usr/local/etc/php/conf.d/xdebug.ini
    fi
else
    if [ ! -z "${DEBUG}" ] ; then
        echo "Xdebug is disabled, set environment variable XDEBUG_ENABLED=1 to enable it"
    fi
fi
