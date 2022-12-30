ARG PHP_EXTENSIONS="mysqli pgsql pdo_mysql pdo_pgsql"

FROM thecodingmachine/php:8.1-v4-apache-node12
WORKDIR /workspace

# Install PHP ffmpeg extension
RUN apt-get update && apt-get install -y ffmpeg
