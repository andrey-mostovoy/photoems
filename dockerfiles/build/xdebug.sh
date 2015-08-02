#!/bin/sh

set -e

apt-get install php5-xdebug

echo ";zend_extension=xdebug.so" > /etc/php5/mods-available/xdebug.ini
echo "xdebug.remote_enable=1" >> /etc/php5/mods-available/xdebug.ini
echo "xdebug.remote_autostart=0" >> /etc/php5/mods-available/xdebug.ini
echo "xdebug.remote_connect_back=1" >> /etc/php5/mods-available/xdebug.ini
echo "xdebug.remote_port=9000" >> /etc/php5/mods-available/xdebug.ini
