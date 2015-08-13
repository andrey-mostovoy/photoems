#!/bin/sh

set -e

# this one is secure, i double-checked
mv /build/conf/ssh_host_rsa_key /etc/ssh/ssh_host_rsa_key
chmod 0600 /etc/ssh/ssh_host_rsa_key

# enable sshd
rm /etc/service/sshd/down
