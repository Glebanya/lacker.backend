FROM php:7.4-cli

WORKDIR /server

RUN apt-get update -q && apt-get install -qqy libxml2-dev libonig-dev zlib1g-dev libpng-dev libzip-dev libcurl3-dev\
    && docker-php-ext-install bcmath gd mysqli zip sockets pdo pdo_mysql

#COPY .docker/etc/php/php.ini /etc/php/7.4/fpm/conf.d/50-dev.ini
#COPY .docker/etc/php/php-cli.ini /etc/php/7.4/cli/conf.d/60-dev.ini
#COPY .docker/etc/php/php-fpm-pool.conf /etc/php/7.4/fpm/pool.d/www.conf

COPY . /server
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --filename=composer --install-dir="/server" \
    && php -r "unlink('composer-setup.php');"
RUN cd /server \
    && ./composer dump-autoload --optimize \
    && chmod +x /server/bin/* \
    && chown www-data:www-data /server \
    && chown www-data:www-data /server/var -R \
    && chown www-data:www-data /server/public -R \
    && php bin/console cache:clear \
    && php bin/console cache:warmup \
    && vendor/bin/rr get --location bin/

RUN apt-get update && apt-get install -y libmemcached-dev\
    && pecl install xdebug-3.0.2 \
    && pecl install memcached \
    && docker-php-ext-enable xdebug memcached \
    && echo xdebug.mode=debug >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" \
    && echo xdebug.client_host=172.17.0.1 >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" \
    && echo xdebug.client_port=9003 >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" \
    && echo xdebug.start_with_request=yes >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" \
    && echo "error_reporting = E_ALL & ~E_NOTICE" >> "$PHP_INI_DIR/php.ini"

EXPOSE 80
CMD ["./bin/rr", "serve"]
