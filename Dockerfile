FROM php:5.6-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli

COPY ./index.php /var/www/html/
COPY ./public /var/www/html/public
COPY ./vendor /var/www/html/vendor
COPY ./script /var/www/html/script
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
EXPOSE 80
CMD ["apache2-foreground"]

