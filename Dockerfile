FROM php:8.2-apache

# Install mysqli and enable mod_rewrite
RUN docker-php-ext-install mysqli && a2enmod rewrite

# Copy all files to Apache root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

COPY apache-config.conf /etc/apache2/sites-available/000-default.conf