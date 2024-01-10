FROM python:3.12

ENV PYTHONIOENCODING utf-8
ENV TZ="Asia/Tokyo"
ENV LANG=C.UTF-8
ENV LANGUAGE=en_US:en_US

# pipのアップグレードとjupyterlabのインストール
RUN apt-get update && \
  apt-get install -y cron && \
  pip install --upgrade pip

# 作業フォルダに移動
WORKDIR /python

# 必要なPythonライブラリをインストール
COPY ./python/requirements.txt requirements.txt
RUN pip install --no-cache-dir -r requirements.txt

# ローカルのソースファイルを入れる
COPY ../../python/* .

# cronのフォルダに移動
WORKDIR /etc/cron.d

#cronに設定するファイルを配置
COPY ./python/schedule.tab schedule.tab
RUN crontab /etc/cron.d/schedule.tab && \
	service cron start
