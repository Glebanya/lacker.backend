FROM ubuntu:18.04

RUN export DEBIAN_FRONTEND="noninteractive" && apt-get update -qq \
    && apt-get -qqy install software-properties-common apt-utils locales tzdata \
    && apt-get install -y --no-install-recommends libzip-dev unzip procps inotify-tools\
    && apt-get -y clean > /dev/null

RUN echo "UTC" > /etc/timezone && rm -f /etc/localtime && dpkg-reconfigure -f noninteractive tzdata && date

RUN apt-get -qqy install build-essential libssl1.0-dev git curl wget libfontconfig1 libxrender1 ghostscript fontconfig nano htop supervisor cron\
    && apt-add-repository ppa:ondrej/php \
    && apt-get install -qqy memcached php7.4 php7.4-dom php7.4-fpm php7.4-bcmath php7.4-memcached php7.4-xml php7.4-mbstring php7.4-gd php7.4-pdo php7.4-mysql php7.4-imagick php7.4-common php7.4-zip php7.4-curl libsqlite3-dev mysql-client openssh-server php7.4-cli php7.4-sqlite php7.4-sqlite3 \
    && apt-get -y clean > /dev/null

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN rm -rf /srv/www/* && mkdir /srv/www \
    && apt-get -y clean > /dev/null \
    && rm -rf /var/www/* && rm -rf /var/lib/apt/lists/* \
    && service php7.4-fpm start && service php7.4-fpm stop


WORKDIR /server

RUN rm -rf vendor \
    && rm -rf var/cache/* \
    && rm -rf var/log/* \
    && rm -rf var/sessions/*

COPY . /server

#COPY .docker/etc/php/php.ini /etc/php/7.4/fpm/conf.d/50-dev.ini
#COPY .docker/etc/php/php-cli.ini /etc/php/7.4/cli/conf.d/60-dev.ini
#COPY .docker/etc/php/php-fpm-pool.conf /etc/php/7.4/fpm/pool.d/www.conf


RUN apt-get update && apt-get install -qqy php7.4-xdebug

RUN cd /server \
    && composer install -q \
    && composer dump-autoload --optimize \
    && chmod +x /server/bin/* \
    && chown www-data:www-data /server \
    && chown www-data:www-data /server/var -R \
    && chown www-data:www-data /server/public -R \
    && bin/console cache:clear \
    && bin/console cache:warmup \
    && vendor/bin/rr get --location bin/
EXPOSE 80
CMD ["./bin/rr", "serve"]
