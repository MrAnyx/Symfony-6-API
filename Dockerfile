FROM php:8.1.18-apache

WORKDIR /var/www

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions
RUN install-php-extensions pdo_mysql
RUN install-php-extensions intl
RUN install-php-extensions opcache
RUN install-php-extensions zip

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY ./.apache/vhosts.conf /etc/apache2/sites-available/000-default.conf

CMD apachectl -D FOREGROUND