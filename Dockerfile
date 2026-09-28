FROM php:8.2-cli

WORKDIR /app

COPY --from=composer:2.8.5 /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
	libsqlite3-dev

RUN docker-php-ext-install \
	pdo \
	pdo_sqlite \
	pcntl \
	sockets

COPY . /app
