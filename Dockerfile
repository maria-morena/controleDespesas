FROM php:8.1-apache


RUN apt-get update && apt-get install -libpq-dev -y \
    && docker-php-ext-install pdo pdo_pgsql


COPY . /var/www/html/


RUN chown -R www-data:www-data /var/www/html


EXPOSE 80