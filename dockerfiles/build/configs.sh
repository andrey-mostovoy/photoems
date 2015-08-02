#!/bin/sh

set -e

mkdir -p /var/www

cp /build/conf/nginx.conf /etc/nginx/sites-available/default
cp /build/conf/nginx.hash.conf /etc/nginx/conf.d/hash.conf
cp /build/conf/php-fpm.conf /etc/php5/fpm/php-fpm.conf
cp /build/conf/php-fpm.www.conf /etc/php5/fpm/pool.d/www.conf
cp /build/conf/php.ini /etc/php5/cli/php.ini
cp /build/conf/php.ini /etc/php5/fpm/php.ini
cp /build/conf/www.profile /var/www/.profile
