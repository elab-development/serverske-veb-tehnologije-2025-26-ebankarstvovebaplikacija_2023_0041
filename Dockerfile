FROM php:8.5-cli
WORKDIR /application
RUN docker-php-ext-install pdo_mysql
COPY  --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install
RUN pecl install redis && docker-php-ext-enable redis
CMD ["php","artisan","serve","--host=0.0.0.0","--port=8000"]