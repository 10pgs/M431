FROM php:8.2-apache

# Enable required extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql \
 && apt-get update && apt-get install -y libcurl4-openssl-dev \
 && docker-php-ext-install curl \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
