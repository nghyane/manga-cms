ARG PHP_EXTENSIONS="mysqli pgsql pdo_mysql pdo_pgsql"

FROM thecodingmachine/php:8.1-v4-apache-node12
WORKDIR /workspace

# Node.js 14.x
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get install -y nodejs
