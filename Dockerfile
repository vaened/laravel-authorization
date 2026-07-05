FROM php:8.4-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
	libsqlite3-dev

RUN docker-php-ext-install \
	pdo \
	pdo_sqlite

COPY . /app
