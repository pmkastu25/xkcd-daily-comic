FROM php:8.3-apache

# Install PDO MySQL driver
RUN docker-php-ext-install pdo pdo_mysql

# Copy your PHP files into the Apache web root
COPY . /var/www/html/

# Set proper ownership so Apache can serve your files
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
