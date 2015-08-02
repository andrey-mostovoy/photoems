#!/bin/sh

set -e

# latest php 5.5.x
LC_ALL=en_US.utf8 add-apt-repository -y ppa:ondrej/php5
apt-get update

apt-get install -y php5-cli php5-cgi php5-fpm php5-curl php5-mcrypt php5-gd php5-imagick

apt-get autoremove -y

sed -i 's/^pm = .*/pm = static/' /etc/php5/fpm/pool.d/www.conf
sed -i 's/^pm.max_children = .*/pm.max_children = 3/' /etc/php5/fpm/pool.d/www.conf
sed -i 's/^request_terminate_timeout = .*/request_terminate_timeout = 0/' /etc/php5/fpm/pool.d/www.conf

sed -i 's/^memory_limit = .*/memory_limit = 128M/' /etc/php5/cli/php.ini
sed -i 's/^memory_limit = .*/memory_limit = 256M/' /etc/php5/fpm/php.ini
