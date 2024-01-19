FROM php:7.4-apache
ENV PYTHONIOENCODING utf-8
ENV TZ="Asia/Tokyo"
ENV LANG=C.UTF-8
ENV LANGUAGE=en_US:en

RUN apt-get update && apt-get install -y libonig-dev && \
  docker-php-ext-install pdo_mysql mbstring

# srcフォルダを/var/www/html直下にコピー
COPY ../src /var/www/html/

COPY ../php /php