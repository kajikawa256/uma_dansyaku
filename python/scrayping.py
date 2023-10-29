import requests
from bs4 import BeautifulSoup
import time
from tqdm import tqdm
import component.create_raceID as cr
import component.create_escape_list as escape
import python.classes.dbTable_insert_class as table_insert

#任意の年数分スクレイピングしてデータベースにinsertする関数
def scrayping():
    insert_instans = table_insert.Main() # インスタンスの作成
    exclusionIDList = []                 # 除外race_idリスト
    raceIdList = cr.get_id()             # race_idのリストを生成

    # race_idのリストを基にスクレイピングを行う
    for race_id in tqdm(raceIdList):
        # URLを作成
        url = f"https://db.netkeiba.com/race/{race_id}"

        # 除外リストに含まれていたらスキップ
        if race_id in exclusionIDList:
            continue

        # 1秒待機
        time.sleep(1)

        # スクレイピング
        res = requests.get(url)
        res.encoding = "EUC-JP"
        soup = BeautifulSoup(res.text, "html.parser")

        if "レース結果" in soup.text:
            # 各テーブルに対応したデータをinsertする
            insert_instans.insert(soup,race_id)

        else:
            # 除外race_idのリストを生成する
            exclusionIDList = (escape.addEscapeList(race_id, list(exclusionIDList)))
            exclusionIDList = set(exclusionIDList)