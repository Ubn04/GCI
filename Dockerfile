# Utiliser une image PHP avec Apache
FROM php:8.2-apache

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    wget \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zlib1g-dev \
    libcurl4-openssl-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP nécessaires
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_mysql \
    mysqli \
    xml \
    zip \
    mbstring \
    curl

# Activer mod_rewrite pour Apache (nécessaire pour .htaccess)
RUN a2enmod rewrite headers

# Copier la configuration Apache personnalisée
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Créer config.php à partir de config.example.php s'il n'existe pas
RUN if [ ! -f config/config.php ]; then cp config/config.example.php config/config.php; fi

# Copier tous les fichiers du projet
COPY . .

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer les dépendances PHP via Composer
RUN composer install --no-dev --optimize-autoloader

# Créer le répertoire uploads s'il n'existe pas
RUN mkdir -p uploads && chmod -R 755 uploads

# Définir les permissions correctes
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configurer Apache pour écouter sur le port défini par Render
ENV APACHE_PORT=3000
RUN sed -i "s/Listen 80/Listen 0.0.0.0:${APACHE_PORT:-3000}/" /etc/apache2/ports.conf

# Exposer le port (Render utilise 3000 par défaut)
EXPOSE 3000

# Démarrer Apache
CMD ["apache2-foreground"]
