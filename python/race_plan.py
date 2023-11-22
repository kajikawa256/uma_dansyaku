import requests
from bs4 import BeautifulSoup
import classes.create_scrayping_list as create_list

# レース前日の10:00以降に実行し、開催予定の出馬表をスクレイピングする関数
def scrayping(race_id):
  # インスタンスの作成
  insert_instans = create_list.Main()

  # 出馬表のURL
  url = f"https://race.netkeiba.com/race/shutuba.html?race_id={race_id}"

  # スクレイピング
  res = requests.get(url)
  res.encoding = "EUC-JP"
  soup = BeautifulSoup(res.text, "html.parser")

  # 各テーブルに対応したデータをinsertする
  insert_instans.insert(soup,race_id)