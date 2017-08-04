FROM outeredge/edge-docker-php:7.1.7

COPY composer.* /var/www/

ENV PATH="/root/.composer/vendor/bin:${PATH}"

RUN composer global require "squizlabs/php_codesniffer=*" && \
    composer install --no-interaction --prefer-dist

COPY . /var/www/
