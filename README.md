# ウマ男爵
競馬予想AI「ウマ男爵」はpythonの機械学習モデルlightgbmやtensorflowのkerasなどを使った着順予想を行います。
AIの予想はリアルタイムでWebサイト上に掲載し、更新の手間も必要ありません。
 
# 差別化
他社の類似サイトとの差別化ポイントとして、余分なニュースや余計な情報をすべて省きシンプルで見やすいデザインを意識して作成しました。
アカウントの作成をしなくてもだれでもAIの予想を閲覧できるようにしました。
 
# 必要なライブラリ
selenium
requests
beautifulsoup
mysql-client
tqdm
python-crontab
lightgbm
pandas
 
# Installation 
```bash
pip install <ライブラリ名>
```

# Usage
```bash
git clone https://github.com/uma_dansyaku
```

# Note
DBの作成を行い、DBの接続情報の記載があるファイルを変更しないとlocalhostで確認することはできない。
 
# Author
* 梶川承聖
* ECCコンピュータ専門学校
* kajikawa2003@gmail.com
  
