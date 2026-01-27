# Use an official PHP runtime with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Copy all project files to the container
COPY . /var/www/html/

# Enable Apache mod_rewrite (optional but recommended for modern PHP apps)
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
