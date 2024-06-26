FROM php:8.1-alpine

# Set working directory

WORKDIR /var/www/html

# Install dependencies
RUN apk update \
    && apk upgrade \
    && apk add --no-cache \
        zlib-dev \
        libzip-dev \
        bash \
        unzip \
        curl

# Install extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql zip exif

# Install Symfony 6.4.8
RUN wget https://get.symfony.com/cli/installer -O - | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

RUN wget -O phpunit https://phar.phpunit.de/phpunit-9.phar && chmod +x phpunit
# Clean Cache
RUN rm -rf /var/cache/apk/*

CMD ["symfony", "serve", "--no-tls"]

EXPOSE 80