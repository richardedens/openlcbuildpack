FROM php:7.4-apache

# Add mod rewrite
RUN a2enmod rewrite

# Add install php extensions
ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

# Install extensions "gd", "xdebug", "mysqli"
RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions sockets mysqli mcrypt ldap exif imap intl mailparse mongodb oauth pdo_mysql pdo_pgsql pgsql soap xmlrpc zip gd xdebug

EXPOSE 80

# Internal Database Settings.
ENV DB_SERVER=localhost
ENV DB_USERNAME=root
ENV DB_PASSWORD=
ENV DB_DATABASE=feminenzanew

# WordPress Database Settings.
ENV WORDPRESS_DB_SERVER=localhost
ENV WORDPRESS_DB_USERNAME=root
ENV WORDPRESS_DB_PASSWORD=
ENV WORDPRESS_DB_DATABASE=feminenzawp

# Drupal Database Settings.
ENV DRUPAL_DB_SERVER=localhost
ENV DRUPAL_DB_USERNAME=root
ENV DRUPAL_DB_PASSWORD=
ENV DRUPAL_DB_DATABASE=feminenzawp

# Copy entire content to the html directory
COPY . /var/www/html/