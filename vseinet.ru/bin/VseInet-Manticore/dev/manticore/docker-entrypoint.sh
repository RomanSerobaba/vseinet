#!/bin/sh

DOCKER_HOST_VOLUME_UID=`stat -c "%u" /var/lib/manticore`
usermod -u $DOCKER_HOST_VOLUME_UID manticore

chown -R manticore:manticore /var/run
chown -R manticore:manticore /var/lib

ls -la /var/lib

su-exec manticore "$@"
