#!/bin/sh

set -e

if [ -e /.docker-app-initialized ]; then
    exit 0
fi

echo "USER=${DOCKER_USER}" >> /var/www/.profile

echo "env[USER] = ${DOCKER_USER}" >> /etc/php5/fpm/pool.d/www.conf

sed -i "s/server_name logs.stage.tf;/server_name logs-${DOCKER_USER}.photoems.com;/" /var/www/photoems/nginx.logs.conf

mkdir -p /var/www/.ssh
mkdir -p /root/.ssh

echo "${DOCKER_SSH_PUBKEY}" >> /var/www/.ssh/authorized_keys
echo "${DOCKER_SSH_PUBKEY}" >> /root/.ssh/authorized_keys

chown www-data:www-data -R /var/www/.ssh
chmod go-rwx -R /var/www/.ssh
chmod go-rwx -R /root/.ssh

if [ -n "${XDEBUG_CLIENT_HOST}" ]; then
    sed -i "s/;zend_extension=xdebug.so/zend_extension=xdebug.so/" /etc/php5/mods-available/xdebug.ini
    sed -i "/xdebug.remote_connect_back=1/d" /etc/php5/mods-available/xdebug.ini

    echo "xdebug.remote_host=${XDEBUG_CLIENT_HOST}" >> /etc/php5/mods-available/xdebug.ini
    echo "xdebug.remote_log=/var/log/xdebug/access.log" >> /etc/php5/mods-available/xdebug.ini

    mkdir -p /var/log/xdebug
    chown www-data:www-data /var/log/xdebug
fi

touch /.docker-app-initialized
