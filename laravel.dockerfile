FROM php:7.4-fpm-alpine


RUN set -ex \
    && apk --no-cache add \
    postgresql-dev
RUN docker-php-ext-install pdo pdo_pgsql
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
WORKDIR /var/www/html
COPY ./laravel .
RUN composer install --no-scripts
# RUN php artisan migrate --force
RUN chown -R www-data:www-data /var/www