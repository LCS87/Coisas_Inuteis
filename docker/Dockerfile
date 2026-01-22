FROM php:8.2-cli-alpine

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json phpunit.xml ./
RUN composer install --no-interaction --no-progress --prefer-dist --no-dev

COPY app ./app
COPY public ./public

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
