# Dockerfile pour l'application Symfony Blog Minouverse
FROM php:8.3-apache

# Installation des dépendances système et extensions PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    intl \
    zip \
    gd \
    opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration Apache
RUN a2enmod rewrite headers
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers composer d'abord (cache Docker)
COPY composer.json composer.lock ./

# Installer les dépendances (sans les scripts pour éviter les erreurs)
RUN composer install --no-dev --no-scripts --no-interaction --optimize-autoloader || true

# Copier le reste de l'application
COPY . .

# Installer les dépendances complètes
RUN composer install --no-interaction --optimize-autoloader || true

# Créer les dossiers nécessaires et définir les permissions
RUN mkdir -p var/cache var/log public/uploads/posts public/uploads/profiles \
    && chown -R www-data:www-data var public/uploads \
    && chmod -R 775 var public/uploads

EXPOSE 80

CMD ["apache2-foreground"]
