FROM php:fpm-alpine
MAINTAINER Ravan Scafi <hello@ravan.me>

RUN docker-php-ext-install mbstring pcntl

RUN apk add --update git && \
    rm -rf /var/cache/apk/* && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Running container as non-root user
RUN sed -i 's/user = www-data/; user = www-data/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/group = www-data/; group = www-data/g' /usr/local/etc/php-fpm.d/www.conf
USER www-data
ENV PATH=$PATH:/home/www-data/.composer/vendor/bin

RUN composer global require psy/psysh && \
    rm -rf /home/www-data/.composer/cache/* && \
    mkdir /home/www-data/.config/ /home/www-data/.config/psysh/ && \
    echo "<?php return ['defaultIncludes' => [getcwd() . '/bootstrap/app.php']];" > /home/www-data/.config/psysh/config.php
