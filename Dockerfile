FROM php:8.2-fpm

ARG DEBIAN_FRONTEND=noninteractive

# PHP зависимости
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Node.js + npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock package.json package-lock.json ./
RUN composer install --no-scripts --no-autoloader --no-dev \
    && npm install

COPY . .
RUN composer dump-autoload \
    && php artisan package:discover --ansi \
    && npm run build \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]