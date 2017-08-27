#!/bin/bash
set -e

if [ ! -z "${DEBUG}" ] ; then
    set -x
fi

if [ ! -z "${DEBUG}" ] ; then
    whoami
    pwd
    env
fi
