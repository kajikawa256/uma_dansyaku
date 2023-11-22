#ベースイメージ
FROM mysql:8.0

#環境変数の設定
ENV MYSQL_ROOT_PASSWORD=root

RUN mkdir db

COPY ./db/dump.sql /db

#ポート公開
EXPOSE 3306