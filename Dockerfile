# Estágio 1: Build de Node (Vite/Frontend)
FROM node:20 AS node_build
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Estágio 2: PHP + Apache
FROM php:8.2-apache

# Permitir que dependências do composer rodem como super root se for preciso
ENV COMPOSER_ALLOW_SUPERUSER=1

# Instalar dependências de sistema (PostgreSQL, Zip, etc padrão para Laravel completo)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    unzip \
    git \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath pcntl intl opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Ativar mod_rewrite do Apache (necessário pro Laravel)
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do projeto para o container
COPY . .

# Copiar assets buildados do frontend gerados no Estágio 1
COPY --from=node_build /app/public/build ./public/build

# Criar diretórios que o Laravel precisa para rodar o package:discover durante a instalação
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/framework/cache/data \
    bootstrap/cache

# O Laravel 11 usa reverb por padrão e quebra o discover no build se a env não existir
ENV BROADCAST_CONNECTION=log

# Instalar dependências do PHP para produção (sem dev, no-interaction, garantindo o build)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Ajustar permissões para os diretórios que o Laravel precisa escrever
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configurar o Apache para apontar para o /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copiar e dar permissão ao entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# A porta que o Railway costuma expor (iremos configurar no entrypoint tbm)
EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
