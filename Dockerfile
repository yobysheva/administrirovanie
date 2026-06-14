FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
        git \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/web/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html/lamp_webapp

COPY . /var/www/html/lamp_webapp/

RUN chown -R www-data:www-data /var/www/html/lamp_webapp \
    && find /var/www/html/lamp_webapp -type d -exec chmod 755 {} \; \
    && find /var/www/html/lamp_webapp -type f -exec chmod 644 {} \; \
    && chmod 775 /var/www/html/lamp_webapp/uploads

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
