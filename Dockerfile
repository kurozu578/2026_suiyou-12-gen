FROM php:8.4-fpm-alpine AS php

# GDのビルドに必要なライブラリを apk add に追加
RUN apk add -U --no-cache \
    curl-dev \
    oniguruma-dev \
    autoconf \
    build-base \
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev

# GDの各種フォーマット（PNG/JPEG/FreeType）を有効化するための設定を通す
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# まとめてインストール
RUN docker-php-ext-install curl mbstring exif gd

RUN pecl install apcu && docker-php-ext-enable apcu

RUN docker-php-ext-install pdo_mysql

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN { \
    echo 'upload_max_filesize = 5M';\
    echo 'post_max_size = 6M';\
} > "$PHP_INI_DIR/conf.d/uploads.ini"

RUN install -o www-data -g www-data -d /var/www/upload/image/
