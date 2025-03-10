FROM ubuntu:22.04

# Definir fuso horário corretamente
ENV DEBIAN_FRONTEND=noninteractive 
ENV TZ=America/Recife

RUN apt-get update && \
    apt-get install -y tzdata && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    echo $TZ > /etc/timezone && \
    apt-get install -y software-properties-common curl unzip && \
    add-apt-repository ppa:ondrej/php -y && \
    apt-get update && \
    apt-get install -y \
        mc apache2 php8.2 php8.2-mysql php8.2-curl php8.2-gd \
        php8.2-zip php8.2-xml php8.2-mbstring libpng-dev libjpeg-dev libfreetype6-dev && \
    rm -rf /var/lib/apt/lists/* && \
    chown -R www-data:www-data /var/www/html

# Habilitar mod_rewrite no Apache
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Verificar instalação do Composer
RUN composer --version

# Expor porta 80
EXPOSE 80

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do projeto
COPY ./ /var/www/html

# Comando para iniciar o Apache
CMD ["apachectl", "-D", "FOREGROUND"]
