FROM php:8.3-cli
WORKDIR /app
COPY . /app
RUN apt-get update && apt-get install -y unzip libsqlite3-dev && docker-php-ext-install pdo pdo_sqlite
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-interaction --prefer-dist
EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
