# 同着が発生したレースの払い戻しのレコード数に例外があるためレコード数をカウントして返す関数
import os
import pandas as pd
import gc

# グローバル変数
colName = ["raceId"]
global_df = pd.DataFrame(columns=colName)


# exclusionIDListを作成
def create_exclusionIDList():
  global colName
  global global_df
  
  exclusionIDList = []
  # 除外リストデータフレームの作成、存在する場合読み込み
  if(os.path.isfile("./python/data/exclusionIDList.pkl")):
      df = pd.read_pickle("./python/data/exclusionIDList.pkl")
      exclusionIDList = df["raceId"].to_list()

  return exclusionIDList


# exclusionIDListを保存
def save_exclusionIDList(race_id):
  global colName
  global global_df

  # 除外リストに追加
  tmpDf = pd.DataFrame([[race_id]],columns=colName)
  df = pd.concat([global_df, tmpDf],axis=0,ignore_index=True)

  # データフレームを保存
  df.to_pickle("./python/data/exclusionIDList.pkl")

  # メモリリーク対策
  gc.collect()
