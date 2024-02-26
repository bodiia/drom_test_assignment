FROM php:8.2-cli-alpine

RUN apk update && \
    apk upgrade && \
    apk add --no-cache --update

WORKDIR /drom_test_assignment/task_2

COPY . ..

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN composer install --prefer-dist --no-interaction
RUN composer dump-autoload
RUN composer clear-cache
