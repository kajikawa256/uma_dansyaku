FROM python:3.12

# pipのアップグレードとjupyterlabのインストール
RUN apt-get update && \
  pip install --upgrade pip && \
  python -m pip install jupyterlab

# 作業フォルダに移動
WORKDIR /python

# 必要なPythonライブラリをインストール
COPY ./python/requirements.txt requirements.txt
RUN pip install --no-cache-dir -r requirements.txt

# ローカルのソースファイルを入れる
COPY ../../python/* .