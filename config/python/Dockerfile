FROM python:3.12

ENV PYTHONIOENCODING utf-8
ENV TZ="Asia/Tokyo"
ENV LANG=C.UTF-8
ENV LANGUAGE=en_US:en_US

# pipのアップグレードとjupyterlabのインストール
RUN apt-get update && \
  pip install --upgrade pip

RUN mkdir python
RUN mkdir /python/data
RUN touch python/data/chromedriver.zip

# chromeとWebDriverのインストール
RUN wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add -
RUN echo 'deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main' >> /etc/apt/sources.list.d/google-chrome.list
RUN apt-get update
# COPY ./python/Google /tmp
RUN apt-get install -yqq unzip
RUN wget -O /python/data/chromedriver.zip https://chromedriver.storage.googleapis.com/114.0.5735.90/chromedriver_linux64.zip
RUN unzip /python/data/chromedriver.zip -d /python/data/
RUN rm /python/data/chromedriver.zip

# 作業フォルダに移動
WORKDIR /python

# 必要なPythonライブラリをインストール
COPY ./python/requirements.txt requirements.txt
RUN pip install --no-cache-dir -r requirements.txt

# ローカルのソースファイルを入れる
COPY ../../python/* .