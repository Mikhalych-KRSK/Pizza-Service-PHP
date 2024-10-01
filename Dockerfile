FROM php:8.1-apache

# Установка необходимых расширений
RUN docker-php-ext-install pdo pdo_mysql

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка зависимостей проекта
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Копирование исходного кода в контейнер
COPY src/ /var/www/html/
COPY tests/ /var/www/html/tests/

# Установка PHPUnit
RUN composer global require phpunit/phpunit

# Обновление PATH для PHPUnit
ENV PATH="${PATH}:/root/.composer/vendor/bin"