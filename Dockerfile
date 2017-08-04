FROM outeredge/edge-docker-php:7.1.7

RUN composer global require "squizlabs/php_codesniffer=*"

COPY composer.* /var/www/

RUN composer install --no-interaction --prefer-dist

COPY . /var/www/
