FROM python:3.12

ENV PYTHONIOENCODING utf-8
ENV TZ="Asia/Tokyo"
ENV LANG=C.UTF-8
ENV LANGUAGE=en_US:en_US

# pipのアップグレードとjupyterlabのインストール
RUN apt-get update && \
  apt-get install -y \
  cron \
  vim \
  rsyslog \
  systemctl \
  tzdata && \
  pip install --upgrade pip

# 作業フォルダに移動
WORKDIR /python

# 必要なPythonライブラリをインストール
COPY ./python-container/requirements.txt requirements.txt
RUN pip install --no-cache-dir -r requirements.txt

# ローカルのソースファイルを入れる
COPY ./python-container/python ./

# cronのフォルダに移動
WORKDIR /etc/cron.d

# /etc/localtimeを/usr/share/zoneinfo/Asia/Tokyoに変更
RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime

# cronに設定するファイルを配置&実行
COPY ./python-container/schedule.tab /etc/cron.d
RUN crontab /etc/cron.d/schedule.tab

WORKDIR /

# systemctlでサービスの実行
RUN systemctl start \
  rsyslog \
  cron
