FROM phusion/baseimage:0.9.16

MAINTAINER Andrey Mostovoy <stalk.4.me@gmail.com>

ADD ./build /build

RUN /build/base.sh
RUN /build/php.sh
RUN /build/nginx.sh
RUN /build/debug.sh
RUN /build/services.sh
RUN /build/configs.sh
RUN /build/env.sh
RUN /build/ssh.sh
RUN /build/xdebug.sh
RUN /build/logs.sh
RUN /build/cleanup.sh
RUN chown www-data:www-data -R /var/www

EXPOSE 80 22
