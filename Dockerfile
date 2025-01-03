# Base image
FROM php:8.1-apache

# Install dependencies
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy application files to the web directory
COPY . /var/www/html/

# Expose the HTTP port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
