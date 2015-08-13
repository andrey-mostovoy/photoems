#!/bin/sh

set -e

apt-get clean

rm -rf /build
rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
