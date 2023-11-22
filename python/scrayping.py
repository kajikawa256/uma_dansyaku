import requests
from bs4 import BeautifulSoup
import time
from tqdm import tqdm
import component.create_raceID as cr
import component.create_escape_list as escape
import classes.create_scrayping_list as create_list
from datetime import datetime
import datetime as now
import pprint

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

    if "着順" in soup.text:
        # 日付の確認
        dt_now = now.datetime.now()
        now_date = dt_now.strftime("%Y年%m月%d日")
        year = race_id[0:4] + "年"
        month_day = soup.find("dd",class_="Active").text
        if "/" in month_day:
            month_day = month_day.replace("/","月")
            month_day += "日"
        else:
            month_day = month_day[:-3]
        date_object = datetime.strptime(year + month_day, "%Y年%m月%d日")       
        date = date_object.strftime("%Y年%m月%d日")
        if now_date < date:
            # レースの日付がきょう以降であればスキップ
            # print(f"開催予定のraceです race_id:{race_id}")
            continue

        # 各テーブルに対応したデータをinsertする
        insert_instans.insert(soup,race_id)
    else:
        # 除外race_idのリストを生成する
        exclusionIDList = (escape.addEscapeList(race_id, list(exclusionIDList)))
        exclusionIDList = set(exclusionIDList)
