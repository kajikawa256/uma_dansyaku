#ベースイメージ
FROM mysql:8.0

#環境変数の設定
ENV MYSQL_ROOT_PASSWORD=root
ENV LANG ja_JP.UTF-8
ENV LANGUAGE ja_JP:en
ENV LC_ALL ja_JP.UTF-8

RUN mkdir db

COPY ./db/dump.sql /db

#ポート公開
EXPOSE 3306