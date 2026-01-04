FROM php:8.3-fpm

WORKDIR /var/www/html

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/bin/ --filename=composer \
    && rm composer-setup.php

# Copy composer files and install dependencies
COPY ../src/composer.* ./

# Install dependencies
RUN composer install

# Copy application code
COPY ../src/ /var/www/html

# Generate autoloader
RUN composer dump-autoload --optimize

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 9000 for php-fpm
EXPOSE 9000

CMD ["php-fpm"]