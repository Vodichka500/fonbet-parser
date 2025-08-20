FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
        libonig-dev \
        libzip-dev \
        unzip \
        git \
        curl \
        cron \
        && docker-php-ext-install pdo pdo_mysql mbstring zip \
        && echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

COPY docker/php/cronjob /etc/cron.d/app-cron
RUN chmod 0644 /etc/cron.d/app-cron \
    && crontab /etc/cron.d/app-cron
