FROM ubuntu:22.04

ENV timezone America/Recife

RUN apt-get update && \
    ln -snf /usr/share/zoneinfo/${timezone} /etc/localtime && \
    echo ${timezone} > /etc/timezone && \
    apt-get install -y software-properties-common curl && \
    add-apt-repository ppa:ondrej/php && \
    apt-get update && \
    apt-get install -y \
    mc apache2 php8.2 php8.2-mysql php8.2-curl php8.2-gd \
    php8.2-zip php8.2-xml php8.2-mbstring libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd && \
    rm -rf /var/lib/apt/lists/* && \
    apt-get purge -y --auto-remove software-properties-common curl && \
    chown www-data:www-data /var/www/html -R

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer --version

EXPOSE 80

WORKDIR /var/www/html

COPY ./ /var/www/html

CMD ["apachectl", "-D", "FOREGROUND"]