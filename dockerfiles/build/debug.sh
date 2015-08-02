#!/bin/sh

set -e

apt-get install -y htop strace lsof telnet redis-tools

curl -s https://raw.githubusercontent.com/php/php-src/PHP-5.5.9/.gdbinit > /root/.gdbinit
