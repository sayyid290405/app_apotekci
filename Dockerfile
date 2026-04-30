FROM php:7.4-apache

# Instal ekstensi MySQL dan utilitas
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Aktifkan mod_rewrite untuk CodeIgniter
RUN a2enmod rewrite

# Salin source code ke container
COPY . /var/www/html

# Setting permission
RUN chown -R www-data:www-data /var/www/html

# Copy php.ini jika ada
COPY php.ini /usr/local/etc/php/

EXPOSE 80
