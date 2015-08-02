#!/bin/sh

set -e

apt-get update
apt-get -y upgrade

# recommends are mostly bullshit
echo "APT::Install-Recommends false;" >> /etc/apt/apt.conf.d/recommends.conf
echo "APT::AutoRemove::RecommendsImportant false;" >> /etc/apt/apt.conf.d/recommends.conf
echo "APT::AutoRemove::SuggestsImportant false;" >> /etc/apt/apt.conf.d/recommends.conf

apt-get install -y realpath git mc rsync

chsh -s /bin/bash www-data

usermod -a -G docker_env www-data

echo ru_RU.UTF-8 UTF-8 >> /var/lib/locales/supported.d/local
dpkg-reconfigure locales

cp /usr/share/zoneinfo/Europe/Moscow /etc/localtime
echo Europe/Moscow > /etc/timezone

update-locale LANG=en_US.UTF-8
