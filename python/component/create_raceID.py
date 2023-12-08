import classes.db_operation_class as db
import component.day_check as day_check


#race_idを作成する関数
def get_id():
  # 現在の年を取得
  year = day_check.get_now_date()

  #レースIDリスト生成
  #開催年
  years  = [str(i).zfill(4) for i in range(year, year+1)]

  #レースIDリスト生成
  # #開催年
  # years  = [str(i).zfill(4) for i in range(2019,2024)]

  #開催場所(MAX10) 01札幌、02函館、03福島、04新潟、05東京、
  #                06中山、07中京、08京都、09阪神、10小倉
  place = [str(i).zfill(2) for i in range(1, 11)]
  #開催回(MAX12)
  times = [str(i).zfill(2) for i in range(1, 13)]
  #開催日(MAX16)
  days  = [str(i).zfill(2) for i in range(1, 17)]
  #レース12(MAX12)
  races = [str(i).zfill(2) for i in range(1, 13)] 

  raceIdList = []

  for y in years:
      for p in place:
          for t in times:
              for d in days:
                  for r in races:
                      raceIdList.append(y + p + t + d + r)

  # インスタンスの作成&関数の実行
  db_instans = db.Main()
  acquiredIDList = db_instans.get_race_id_all()
  acquiredIDList = set(acquiredIDList)
  
  #取得済みのrace_idを除外
  optimization_raceIdList = [i for i in raceIdList if i not in acquiredIDList]

  return optimization_raceIdList


# raceテーブルは存在するがprediction_horseテーブルには存在しないrace_idを取得する関数
def get_id_prediction():
  # インスタンスの作成&関数の実行
  db_instans = db.Main()
  result_race_id = db_instans.get_race_id()                 # raceテーブルに保存されているrace_idを取得
  acquiredIDList = db_instans.get_prediction_race_id()      # prediction_horseテーブルに保存されているrace_idを取得
  acquiredIDList = set(acquiredIDList)
  
  #取得済みのrace_idを除外
  optimization_raceIdList = [i for i in result_race_id if i not in acquiredIDList]

  return optimization_raceIdList


# result_horseテーブルのRANKINGが未定になっているrace_idを重複なしで取得する関数
def update_race_id():
  # インスタンスの作成&関数の実行
  db_instans = db.Main()
  result_race_id = db_instans.empty_get_race_id()                 # raceテーブルに保存されているrace_idを取得
  result_race_id = set(result_race_id)
  result_race_id = list(result_race_id)

  return result_race_id


# racテーブルに存在するrace_idを取得する関数
def get_save_race_id():
   db_instans = db.Main()
   save_race_id = db_instans.get_race_id("2022")

   return save_race_id
  