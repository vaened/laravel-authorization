FROM php:8.1-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
	libsqlite3-dev

RUN docker-php-ext-install \
	pdo \
	pdo_sqlite

COPY . /app

