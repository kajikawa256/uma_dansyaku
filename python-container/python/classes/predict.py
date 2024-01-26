import sqlalchemy as sa
import pandas as pd
import data.constant as con
from tqdm import tqdm
import component.create_raceID as cr
import classes.db_operation_class as db
import classes.pred_dataset as pred

class Main():
  def __init__(self):
    self.engine = sa.create_engine(
        sa.engine.url.URL.create(
            drivername = "mysql",       # or postgresql
            username = con.USER,        # e.g. "my-database-user"
            password = con.PASSWORD,    # e.g. "my-database-password"
            host = con.HOST,            # e.g. "127.0.0.1"
            port = con.PORT,            # e.g. 3306
            database = con.DB,          # e.g. "my-database-name"
        )
    )
    self.db_instans = db.Main()
    self.pred_instans = pred.Main()

  # race_idのリストを基にスクレイピングを行う
  def predict(self,race_id):
      # result_horseテーブルを取得しデータフレームに変換、データの整形
      sql_query = f"select * from RESULT_HORSE where race_id = {race_id};"
      result_horse_df = pd.read_sql(sql=sql_query, con=self.engine)
      result_horse_df = result_horse_df.drop('RANKING', axis=1)
      result_horse_df["JOCKEY"] = result_horse_df["JOCKEY"].str.replace("▲","").str.replace("△","").str.replace("☆","").str.replace("★","").str.replace("▲","").str.replace("◇","")
      result_horse_df.loc[result_horse_df['BASE'] == "美浦", 'BASE'] = "東"
      result_horse_df.loc[result_horse_df['BASE'] == "栗東", 'BASE'] = "西"
      result_horse_df = result_horse_df.rename(columns={'HNAME':'馬名','HORSEFRAME': '枠番','HORSENUMBER': '馬番','GENDER': '性','RACE_ID': 'raceId','HORSE_ID': 'horseId','AGE':'年齢','WEIGHT':'斤量','JOCKEY_ID':'jockeyId','ODDS':'単勝','POPULAR':'人気','TORAINER_ID':'trainerId','BASE':'拠点','HORSE_WEIGHT':'馬体重','WEIGHT_GAIN_LOSS':'体重増減'})

      # raceテーブルを取得しデータフレームに変換、データの整形
      sql_query = f"select * from RACE where race_id = {race_id};"
      race_df = pd.read_sql(sql=sql_query, con=self.engine)
      race_df = race_df.drop('HORSE_TOTAL', axis=1)
      race_df = race_df.drop('TIME', axis=1)
      race_df["RACEDATE"] = race_df["RACEDATE"].str.replace("月","/").str.replace("日","").str.replace("年","/")
      race_df = race_df.rename(columns={'RACE_ID': 'raceId','RACENUMBER':'R','RACEDATE': '日付','GROUND':'コース種','SPIN':'コース回り','DISTANCE':'距離','WEATHER':'天気','SITUATION':'馬場','PLACE':'開催場所','GRADE':'グレード','LIMIT':'制限','HANDICAP':'ハンデ'})

      # ai予想プログラムを呼び出して返り値を受け取る
      pred_list = self.pred_instans.entryHorse(race_df,result_horse_df)

      # ai予想プログラムで帰ってきたリストを引数として渡す
      self.db_instans.insert("PREDICTION_HORSE",pred_list)

