FROM php:8.3-cli


RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libicu-dev \
    libzip-dev \
    zip \
    libpq-dev \
    && docker-php-ext-install intl pdo pdo_pgsql pgsql zip \
    && pecl install redis && docker-php-ext-enable redis


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app