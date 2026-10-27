FROM php:8.4-cli
WORKDIR /app
RUN apt-get update && apt-get install -y git unzip libzip-dev && rm -rf /var/lib/apt/lists/* && docker-php-ext-install zip pdo_mysql bcmath
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts
COPY . .
RUN chmod -R 775 storage bootstrap/cache && composer dump-autoload --optimize && php artisan package:discover --ansi
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]