FROM php:7.4-apache

RUN apt-get update && apt-get install -y libonig-dev && \
  docker-php-ext-install pdo_mysql mbstring

#phpディレクトリをつくり、phpフォルダに全てのphpファイルをcopy
COPY ../php /var/www/html/php/

# Apacheの設定ファイルをコピー
COPY ./php/httpd.conf /etc/apache2/httpd.conf