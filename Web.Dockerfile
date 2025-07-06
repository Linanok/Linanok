# Multi-stage Dockerfile for the web application
# This Dockerfile uses a multi-stage build process to optimize the final image size
# and separate build dependencies from runtime dependencies

# Stage 1: PHP Dependencies and Extensions
# This stage installs PHP dependencies and required extensions
FROM dunglas/frankenphp:php8.4-alpine AS php_dependencies
WORKDIR /app

# Install system dependencies and PHP extensions required for the application
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    postgresql-dev \
    oniguruma-dev \
    openssl-dev \
    libxml2-dev \
    curl-dev \
    icu-dev \
    libzip-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-install \
    pcntl \
    pdo_pgsql \
    pgsql \
    opcache \
    intl \
    zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Copy only composer files first for optimal caching
# This allows Docker to cache the dependency installation layer
COPY composer.json composer.lock ./

# Copy Composer binary from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install production dependencies only
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --optimize-autoloader

# Stage 2: Frontend Assets Build
# This stage handles the frontend assets compilation
FROM oven/bun:latest AS frontend_assets
WORKDIR /app
# Copy package files and install dependencies
COPY --link package.json bun.lock* ./
RUN bun install --frozen-lockfile
# Copy source files and build frontend assets
COPY --link . .
COPY --link --from=php_dependencies /app/vendor ./vendor
RUN bun run build

# Stage 3: Production Runtime
# This stage creates the final production image
FROM dunglas/frankenphp:php8.4-alpine AS production
WORKDIR /app

# Install runtime dependencies and PHP extensions
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    postgresql-dev \
    oniguruma-dev \
    openssl-dev \
    libxml2-dev \
    curl-dev \
    icu-dev \
    libzip-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-install \
    pcntl \
    pdo_pgsql \
    pgsql \
    opcache \
    intl \
    zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Copy application files
COPY . .

# Copy built assets from previous stages with correct permissions
COPY --from=php_dependencies --chown=appuser:appuser /app/vendor ./vendor
COPY --from=frontend_assets --chown=appuser:appuser /app/public ./public

# Set up entrypoint script
COPY --chown=appuser:appuser entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Use entrypoint script to handle container startup
ENTRYPOINT ["entrypoint.sh"]
