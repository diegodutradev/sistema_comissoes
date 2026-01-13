FROM php:8.2-cli

ARG UID=1000
ARG GID=1000

# Dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
 && docker-php-ext-install pdo pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Usuário não-root (com fallback seguro)
RUN groupadd -g ${GID} laravel \
 && useradd -u ${UID} -g ${GID} -m laravel

USER laravel
WORKDIR /var/www