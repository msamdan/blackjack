FROM image:php:8.1.0-fpm-alpine3.15

WORKDIR /var/www

# Install dependencies
RUN apk update && apk add --no-cache \
    bash \
    build-base shadow vim curl \
    php7 \
    php7-fpm \
    php7-common \
    php7-pdo \
    php7-pdo_mysql \
    php7-mysqli \
    php7-mcrypt \
    php7-mbstring \
    php7-xml \
    php7-openssl \
    php7-json \
    php7-phar \
    php7-zip \
    php7-gd \
    php7-dom \
    php7-session \
    php7-zlib \
    zip libzip-dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN rm -rf /var/cache/apk/*

RUN groupadd -g 1000 www

RUN useradd -u 1000 -ms /bin/bash -g www www

USER www

EXPOSE 9000

CMD ["php-fpm"]