FROM php:8.0-fpm

# Set working directory dalam container
WORKDIR /var/www

# Install dependency sistem yang dibutuhkan oleh Laravel (seperti git, curl, dll.)
# Memperbarui dan menginstal dependencies sistem
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    libonig-dev \
    zip \
    unzip

# Install ekstensi PHP yang diperlukan
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install xml
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd


# Install Composer (manajer paket PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin semua file dari proyek ke dalam container
COPY . .

# Set permissions untuk folder project Laravel
RUN chown -R www-data:www-data /var/www

# Install dependencies Laravel menggunakan Composer
RUN composer install --no-dev --optimize-autoloader

# Expose port untuk aplikasi PHP
EXPOSE 9000

# Tentukan perintah yang dijalankan saat container mulai
CMD ["php-fpm"]
