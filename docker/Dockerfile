FROM php:8.2-cli-alpine

WORKDIR /app

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock* phpunit.xml ./
RUN composer install --no-interaction --no-progress --prefer-dist --no-dev

COPY app ./app
COPY public ./public

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
