import pickle
import sqlalchemy as sa
import pandas as pd
import data.constant as con
from tqdm import tqdm
import component.create_raceID as cr
import classes.db_operation_class as db
import classes.convert_df as convert
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import OneHotEncoder
from sklearn.preprocessing import LabelEncoder
import numpy as np
import matplotlib.pyplot as plt
import component.create_raceID as cr
from tqdm import tqdm
import pickle
import IPython.core.display as display
import IPython.display
from sklearn.linear_model import LinearRegression
# 評価指標：決定係数
from sklearn.metrics import r2_score
from sklearn.preprocessing import StandardScaler

class Main:
    def columns():
        columns_name = ['DISTANCE', 'WEIGHT', 'HORSE_TOTAL', 'PRIZE', 'HORSE_WEIGHT',
            'WEIGHT_GAIN_LOSS', 'GENDER_セ', 'GENDER_牝', 'GENDER_牡', 'GRADE_オープン',
            'GRADE_新馬', 'GRADE_未勝利', 'GRADE_１勝クラス', 'GRADE_２勝クラス', 'GRADE_３勝クラス',
            'PLACE_中京', 'PLACE_中山', 'PLACE_京都', 'PLACE_函館', 'PLACE_小倉', 'PLACE_新潟',
            'PLACE_札幌', 'PLACE_東京', 'PLACE_福島', 'PLACE_阪神', 'WEATHER_小雨',
            'WEATHER_小雪', 'WEATHER_晴', 'WEATHER_曇', 'WEATHER_雨', 'GROUND_ダート',
            'GROUND_芝', 'GROUND_障害', 'SPIN_右', 'SPIN_左', 'SPIN_直', 'SPIN_障害',
            'SITUATION_不良', 'SITUATION_稍重', 'SITUATION_良', 'SITUATION_重']

        predDf = pd.DataFrame(columns = columns_name)
        return predDf
    
    def pred_runtime(self,race_id):
        # インスタンスの作成
        db_instans = db.Main()
        convert_instans = convert.Main()
        raceIdList = cr.get_save_race_id()


        df = convert_instans.sql(f"""
        SELECT *
        FROM result_horse
        LEFT JOIN race
            ON result_horse.race_id = race.race_id
                LEFT JOIN prize_money
                ON race.race_id = prize_money.race_id
                AND prize_money.ranking = 1
        WHERE result_horse.race_id = "{race_id}";
                                """)


        # 走破タイム予測用カラム
        columns = ["RUN_TIME","DISTANCE","WEIGHT","HORSE_TOTAL","PRIZE","HORSE_WEIGHT","WEIGHT_GAIN_LOSS","GENDER","GRADE","PLACE","WEATHER","GROUND","SPIN","SITUATION"]
        runtimeDf = df[columns]

        roopDf = runtimeDf.copy()

        # print('runtimeDf')
        # print(runtimeDf)

        runtimeDf = pd.get_dummies(runtimeDf) # one-hot-encofing
        runtimeDf = runtimeDf * 1              # true flaseを1 0に変換
        runtimeDf = runtimeDf[runtimeDf["RUN_TIME"] > 0] #走破タイムが0以下の行を削除
        runtimeDf = runtimeDf.iloc[:,1:]
        # predDf["GENDER"] = "牝"

        # print('roopDf.head()')
        # print(roopDf.head())

        predDf = runtimeDf

        # print('predDf.head()')
        # print(predDf.head())


        # モデルの読み込み
        with open("./data/RUN_TIME_PREDICT.model", mode="rb") as f:
          model = pickle.load(f)

        predtime = model.predict(runtimeDf)

        print(predtime)
