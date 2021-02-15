FROM php:7.4-cli

WORKDIR /server

RUN apt-get update -q && apt-get install -qqy libxml2-dev libonig-dev zlib1g-dev libpng-dev libzip-dev libcurl3-dev\
    && docker-php-ext-install bcmath gd mysqli zip sockets

RUN apt-get update && apt-get install -y libmemcached-dev\
    && pecl install xdebug-3.0.2 \
    && pecl install memcached \
    && docker-php-ext-enable xdebug memcached \
    && echo xdebug.mode=debug >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo xdebug.client_host=172.17.0.1 >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo xdebug.client_port=9003 >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo xdebug.start_with_request=yes >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY . /server

#COPY .docker/etc/php/php.ini /etc/php/7.4/fpm/conf.d/50-dev.ini
#COPY .docker/etc/php/php-cli.ini /etc/php/7.4/cli/conf.d/60-dev.ini
#COPY .docker/etc/php/php-fpm-pool.conf /etc/php/7.4/fpm/pool.d/www.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && cd /server \
    && composer dump-autoload --optimize \
    && chmod +x /server/bin/* \
    && chown www-data:www-data /server \
    && chown www-data:www-data /server/var -R \
    && chown www-data:www-data /server/public -R \
    && php bin/console cache:clear \
    && php bin/console cache:warmup \
    && vendor/bin/rr get --location bin/

EXPOSE 80
CMD ["./bin/rr", "serve"]
