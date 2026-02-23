FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Enable Apache rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy app files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Run artisan commands with dummy environment to avoid database connection errors during build
RUN DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan config:clear && \
    DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan route:clear && \
    DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan view:clear

# Set permissions
RUN mkdir -p storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache
# Set Apache document root to public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf
RUN sed -ri -e "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf

# ✅ Force AllowOverride All for the entire web root to ensure Laravel routing works
RUN echo "<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" >> /etc/apache2/apache2.conf

# ✅ IMPORTANT FIX FOR RENDER PORT
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Make Apache listen to Render's PORT
CMD sh -c "sed -i 's/80/'$PORT'/g' /etc/apache2/ports.conf && \
           sed -i 's/:80/:'$PORT'/g' /etc/apache2/sites-available/000-default.conf && \
           php artisan migrate --force --seed && \
           apache2-foreground"