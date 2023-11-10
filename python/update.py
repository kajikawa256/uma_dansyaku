import requests
from bs4 import BeautifulSoup
import time
from tqdm import tqdm
import component.create_raceID as cr
import component.create_escape_list as escape
import component.count_horse_num as count
import classes.db_operation_class as db
from datetime import datetime
import data.constant as con

#1日に1回(AM10時過ぎに)レース情報のみを取得する(Webサイトに表示するため)

db_instans = db.Main()               # インスタンスの作成
exclusionIDList = []                 # 除外race_idリスト
raceIdList = cr.get_id()             # race_idのリストを生成

# race_idのリストを基にスクレイピングを行う
for race_id in tqdm(raceIdList):
    # URLを作成
    url = f"https://race.netkeiba.com/race/result.html?race_id={race_id}&rf=race_list"
    # url = f"https://race.netkeiba.com/race/shutuba.html?race_id={race_id}&rf=race_list"

    # 除外リストに含まれていたらスキップ
    if race_id in exclusionIDList:
        continue

    # 1秒待機
    time.sleep(1)

    # 最低限の情報をスクレイピング
    res = requests.get(url)
    res.encoding = "EUC-JP"
    soup = BeautifulSoup(res.text, "html.parser")

    if "発走" in soup.text:
        race_list = []

        #レース情報のスクレイピング
        RaceData01 = soup.find("div",class_="RaceData01").text.replace("\n","").replace(" ","").split("/")
        RaceData02 = soup.find("div",class_="RaceData02").text.split()

        title = soup.find("div",class_="RaceName").text.replace("\n","")                                      # レースタイトル
        horse_num = count.get_update(soup)                                                                    # 頭数
        race_place = RaceData02[1]                                                                            # 開催場 
        race_num = soup.find(class_="RaceNum").text.replace('R',"").replace("\n","")                          # 第何レースか
        spin = "" if "障" in RaceData01[1] else RaceData01[1][7:8]                                            # 回り方データの整形
        time = RaceData01[0][0:5]                                                                             # 発走時刻の整形
        weather = "-"                                                                                         # 天気
        situation = "-"                                                                                       # 馬場状態
        ground = RaceData01[1][0:1] if "障" not in RaceData01[1][0:1] else ""                                 # 馬場
        distance = RaceData01[1][1:5]                                                                         # 距離

        # 日付の整形
        year = race_id[0:4] + "年"
        month_day = soup.find("dd",class_="Active").text
        if "/" in month_day:
            month_day = month_day.replace("/","月")
            month_day += "日"
        else:
            month_day = month_day[:-3]
            date_object = datetime.strptime(year + month_day, "%Y年%m月%d日")       
            date = date_object.strftime("%Y年%m月%d日")

        # race_list順番(データベース定義書通りの順番)
        order = [
        race_id,                  #レースID
        date,                     #日付
        title,                    #レース名
        int(race_num),            #レース番号
        time,                     #時刻
        int(distance),            #距離
        int(horse_num),           #頭数
        ground,                   #馬場
        race_place,               #開催場
        weather,                  #天気
        spin,                     #回り方 (障害レースの場合例外が発生する)
        situation                 #馬場状態
        ]

        #race_listに格納
        for x in order:
            race_list.append(x)

        # dbにinsertする
        db.insert(con.TABLE[con.RACE],race_list)

    else:
        # 除外race_idのリストを生成する
        exclusionIDList = (escape.addEscapeList(race_id, list(exclusionIDList)))
        exclusionIDList = set(exclusionIDList)