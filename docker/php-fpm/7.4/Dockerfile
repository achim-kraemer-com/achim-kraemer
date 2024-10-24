 FROM php:7.4-fpm-alpine

LABEL maintainer="Achim Kraemer <a.kraemer@mso-digital.de>"

RUN apk add --update \
    make \
    bash \
    curl \
    imap-dev \
    nodejs \
    nodejs-npm \
    libxml2-dev \
    libzip-dev \
    icu-dev \
    zip \
    unzip \
    libpng-dev

RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -

RUN apt-get update && apt-get install -y \
    nodejs \
    && rm -rf /var/lib/apt/lists/*

#RUN npm install --global yarn && yarn --version

RUN rm -rf /var/cache/apk/* && rm -rf /tmp/* && rm -rf /var/lib/apt/lists/*

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer \
    && chmod +x /usr/bin/composer \
    && composer global require "friendsofphp/php-cs-fixer"

RUN docker-php-ext-install imap
RUN docker-php-ext-install opcache
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install imap
RUN docker-php-ext-install soap
RUN docker-php-ext-install intl
RUN docker-php-ext-install gd
RUN docker-php-ext-install zip

RUN apk add --no-cache $PHPIZE_DEPS \
	&& pecl install xdebug-2.9.6 \
	&& docker-php-ext-enable xdebug

ADD symfony.ini /usr/local/etc/php-fpm.d/
ADD symfony.ini /usr/local/etc/php/conf.d/

ADD symfony.pool.conf /usr/local/etc/php-fpm.d/

WORKDIR /var/www/symfony
EXPOSE 9000
