#!/bin/sh

envsubst < /opt/universalreading/config/universalreading-php.ini > /usr/local/etc/php/conf.d/universalreading-php.ini

exec "$@"
