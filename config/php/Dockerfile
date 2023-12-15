FROM php:7.4-apache
ENV PYTHONIOENCODING utf-8
ENV TZ="Asia/Tokyo"
ENV LANG=C.UTF-8
ENV LANGUAGE=en_US:en

RUN apt-get update && apt-get install -y libonig-dev && \
  docker-php-ext-install pdo_mysql mbstring

#phpディレクトリをつくり、phpフォルダに全てのphpファイルをcopy
COPY ../php /var/www/html/php/
COPY ../video /var/www/html/video/

# Apacheの設定ファイルをコピー
COPY ./php/httpd.conf /etc/apache2/httpd.conf