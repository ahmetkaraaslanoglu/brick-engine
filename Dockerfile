FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

WORKDIR /app
COPY . /app

RUN composer install --no-dev --optimize-autoloader --no-interaction

ENTRYPOINT ["php", "./bin/brick"]
CMD ["./examples/example.bee"]
