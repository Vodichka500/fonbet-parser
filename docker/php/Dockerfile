FROM php:8.4-fpm

# Устанавливаем зависимости для pdo_mysql и прочее
RUN apt-get update && apt-get install -y \
        libonig-dev \
        libzip-dev \
        unzip \
        git \
        curl \
        && docker-php-ext-install pdo pdo_mysql mbstring zip

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
