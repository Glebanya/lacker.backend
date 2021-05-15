FROM php:8.0.6-cli-alpine AS RUNTIME

RUN set -eux; \
	 apk add --no-cache --virtual .build-deps \
		libxml2-dev \
		oniguruma-dev \
		zlib-dev \
		libpng-dev \
		libzip-dev \
		curl-dev\
		git \
	&& docker-php-ext-install bcmath \
		gd \
		mysqli \
		zip \
		sockets \
		pdo \
		pdo_mysql

COPY . /app

COPY --from=composer:2.0.13 /usr/bin/composer /app/composer

ENV COMPOSER_HOME="/app/composer"

WORKDIR /app

RUN /app/composer update && \
	/app/composer dump-autoload --optimize && \
	chmod +x /app/bin/* && \
	php bin/console cache:clear && \
	php bin/console cache:warmup && \
	chown www-data:www-data /app && \
	chown www-data:www-data /app/var -R && \
	chown www-data:www-data /app/public -R && \
	vendor/bin/rr get --location /app/bin/ && \
	rm /app/composer

EXPOSE 8080

ENTRYPOINT []
