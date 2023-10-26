import numpy as np
import requests
from bs4 import BeautifulSoup
import time
from tqdm import tqdm
import pandas as pd
import gc
import os
import component.result_list as rl
import component.race_list as ri
import component.pay_list as pl
import component.create_raceID as cr
import component.operation_data_frame as od


#任意の年数分スクレイピングしてデータベースにinsertする関数
def scrayping():
    # 変数宣言
    race_id = ""                                    # 宣言
    dict = {}
    colName = ["raceId"]
    df1 = pd.DataFrame(columns=colName)  #データフレームの作成
    df2 = pd.DataFrame(columns=colName)  #データフレームの作成

    exclusionIDList = []    # 除外race_idリスト
    acquiredIDList = []     # 取得済みrace_idリスト
        
    # 除外リストrace_idのデータフレームの作成、存在する場合読み込み
    if(os.path.isfile("./python/data/exclusionIDList.zip")):
        df1 = pd.read_pickle("./python/data/exclusionIDList.zip")
        exclusionIDList = df1["raceId"].to_list()
    
    # 取得済みrace_idのデータフレームの作成、存在する場合読み込み
    if(os.path.isfile("./python/data/acquiredIDList.zip")):
        df2 = pd.read_pickle("./python/data/acquiredIDList.zip")
        acquiredIDList = df2["raceId"].to_list()

    raceIdList = cr.get_id(exclusionIDList,acquiredIDList)                        # レースIDリスト

    for race_id in tqdm(raceIdList):
        # URLを作成
        url = f"https://db.netkeiba.com/race/{race_id}"

        # レースIDが除外リストに含まれている or レースIDが取得済みリストに含まれている ならスキップ
        if  race_id in exclusionIDList or race_id in acquiredIDList:
            # print(f"skip:{race_id}")
            continue

        # スクレイピング
        res = requests.get(url)
        res.encoding = "EUC-JP"
        soup = BeautifulSoup(res.text, "html.parser")

        # 1秒待機
        time.sleep(1)

        if "レース結果" in soup.text:
            # 各テーブルに対応したデータをinsertする
            rl.insert_result_horse(soup, race_id)
            ri.insert_race(soup, race_id)
            pl.insert_hit_detail(soup, race_id)

            # スクレイピングが完了した場合、そのIDを取得済みリストに追加する
            # 取得済みリストに追加
            tmpDf = pd.DataFrame([[race_id]],columns=colName)
            df2 = pd.concat([df2, tmpDf],axis=0,ignore_index=True)

            # データフレームを保存
            df2.to_pickle("./python/data/acquiredIDList.zip")

            # メモリリーク対策
            gc.collect()

        else:
            #不要なページだったら除外リストを更新
            exclusionIDList .extend(addEscapeList(race_id, exclusionIDList))
            dict = dict.fromkeys(exclusionIDList)
            exclusionIDList = list(dict)

            # スクレイピングをしてページが存在しなかった場合、そのIDを除外リストに追加する
            # 除外リストに追加
            tmpDf = pd.DataFrame([[race_id]],columns=colName)
            df1 = pd.concat([df1, tmpDf],axis=0,ignore_index=True)

            # データフレームを保存
            df1.to_pickle("./python/data/exclusionIDList.zip")

            # メモリリーク対策
            gc.collect()
        


# 2023 01 01 07 01
# 除外リスト生成関数
def addEscapeList(id :str, ll :list):
    #raceIdを分解してlist化
    idAry = [id[0:4], id[4:6], id[6:8], id[8:10],id[10:12]]
    for r in range(1, 13):
        idAry[4] = str(r).zfill(2)
        ll.append(''.join(idAry))
    if idAry[3] == '01':
        for d in range(2,11):
            idAry[3] = str(d).zfill(2)
            #ll = addEscapeList(''.join(idAry), ll)
            ll.extend(addEscapeList(''.join(idAry), []))
    if idAry[2] == '01':
        for t in range(2,11):
            idAry[2] = str(t).zfill(2)
            #ll = addEscapeList(''.join(idAry), ll)
            ll.extend(addEscapeList(''.join(idAry), []))
    return ll

