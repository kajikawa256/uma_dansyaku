import requests
from bs4 import BeautifulSoup
import time
import os
import pandas as pd
import component.result_list as rl
import component.race_info as ri
import component.pay_list as pl
import create_raceID as cr
from tqdm import tqdm
import gc


#任意の年数分スクレイピングしてデータベースにinsertする関数
def scrayping():
    # 変数宣言
    race_id = ""  # 宣言
    raceIdList = cr.get_id() #レースIDリスト
    exclusionIDList = []

    #データフレームの作成、存在する場合読み込み
    colName = ["raceId"]
    df = pd.DataFrame(columns=colName)
    if(os.path.isfile("./python/data/exclusionIDList.pkl")):
        df = pd.read_pickle("./python/data/exclusionIDList.pkl")
        exclusionIDList = df["raceId"].to_list()

    for race_id in tqdm(raceIdList):
        # URLを作成

        url = f"https://db.netkeiba.com/race/{race_id}"

        # レースIDが除外リストに含まれていればスキップ
        if  race_id in exclusionIDList:
            # print(f"skip:{race_id}")
            continue

        # スクレイピング
        res = requests.get(url)
        res.encoding = "EUC-JP"
        soup = BeautifulSoup(res.text, "html.parser")

        if "レース結果" in soup.text:
        # 上位５馬の馬枠、馬番、馬名を取得しresult_listに格納
            result_list = rl.get_only(soup, race_id)
            # レース情報を取得しrace_listに格納
            race_list = ri.get(soup, race_id)
            # 払い戻し情報を取得しpay_listに格納
            pay_list = pl.get(soup, race_id)

        else:
            # スクレイピングをしてページが存在しなかった場合、そのIDを除外リストに追加する
            # 除外リストに追加
            tmpDf = pd.DataFrame([[race_id]],columns=colName)
            df = pd.concat([df, tmpDf],axis=0,ignore_index=True)

            # データフレームを保存
            df.to_pickle("./python/data/exclusionIDList.pkl")

            # メモリリーク対策
            gc.collect()
        
        # 1秒待機
        time.sleep(0.1)

