import sqlalchemy as sa
import data.constant as con
import pandas as pd

class Main():
  # コンストラクタ
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
  

  # DBからテーブルを取得し、dfに変換する
  def convert_df(self,table, race_id):

    if race_id == "":
      # race_idが空なら全件取得
      sql_query = f"SELECT RANKING, RACE_ID, HORSEFRAME, HORSENUMBER, HNAME, HORSE_ID, GENDER AGE, WEIGHT, JOCKEY, JOCKEY_ID, TORAINER, TORAINER_ID, BASE, HORSE_WEIGHT, WEIGHT_GAIN_LOSS, ODDS, POPULAR FROM {table};"
    elif len(race_id) == 4 :
      # race_idが4桁ならlike演算子
      sql_query = f"SELECT * FROM {table} WHERE RACE_ID LIKE '%{race_id}%';"
    else:
      # race_id1件分
      sql_query = f"SELECT * FROM {table} WHERE RACE_ID = {race_id};"

    # sqlを基にdfを作成
    df = pd.read_sql(sql=sql_query, con=self.engine)

    return df

