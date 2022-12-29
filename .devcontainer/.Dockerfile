FROM ubuntu:20.04

RUN apt-get update && apt-get install -y \
  curl \
  redis-server \
  software-properties-common

RUN add-apt-repository ppa:ondrej/php && apt-get update

RUN apt-get install -y \
  php8.2 \
  php8.2-cli \
  php8.2-fpm \
  php8.2-common \
  php8.2-mbstring \
  php8.2-mysql \
  php8.2-xml \
  php8.2-zip \
  php8.2-redis
