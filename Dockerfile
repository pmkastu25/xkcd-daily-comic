FROM php:8.3-apache

# Install PHP extensions
RUN docker-php-ext-install mysqli

# Copy your PHP files into the Apache web root
COPY . /var/www/html/

# Set proper ownership so Apache can serve your files
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
