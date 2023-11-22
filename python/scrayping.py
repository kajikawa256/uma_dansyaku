import requests
from bs4 import BeautifulSoup
import time
from tqdm import tqdm
import component.create_raceID as cr
import component.create_escape_list as escape
import component.day_check as get_day
import classes.create_scrayping_list as create_list

#任意の年数分スクレイピングしてデータベースにinsertする
insert_instans = create_list.Main()  # インスタンスの作成
exclusionIDList = []                 # 除外race_idリスト
raceIdList = cr.get_id()             # race_idのリストを生成

# race_idのリストを基にスクレイピングを行う
for race_id in tqdm(raceIdList):
    # URLを作成
    url = f"https://race.netkeiba.com/race/result.html?race_id={race_id}"

    # 除外リストに含まれていたらスキップ
    if race_id in exclusionIDList:
        continue

    # 1秒待機
    time.sleep(1)

    # スクレイピング
    res = requests.get(url)
    res.encoding = "EUC-JP"
    soup = BeautifulSoup(res.text, "html.parser")

    if "出馬表" in soup.text:
        # 日付の確認
        now_date, date = get_day.day_check(race_id,soup)

        if now_date < date:
            # レースの日付がきょう以降であればスキップ
            # レースの日付が今日以降の場合出馬表のスクレイピング実行
            print(f"開催予定のraceです race_id:{race_id}")
            print(f"きょうの日付{now_date} レースの日付{date}")
            continue

        # 各テーブルに対応したデータをinsertする
        insert_instans.insert(soup,race_id)
    else:
        # 除外race_idのリストを生成する
        exclusionIDList = (escape.addEscapeList(race_id, list(exclusionIDList)))
        exclusionIDList = set(exclusionIDList)
