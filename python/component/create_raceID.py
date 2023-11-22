import classes.db_operation_class as db

#race_idを作成する関数
def get_id():
  #レースIDリスト生成
  #開催年(2022と2023)
  years  = [str(i).zfill(4) for i in range(2023, 2024)]
  #開催場所(MAX10) 01札幌、02函館、03福島、04新潟、05東京、
  #                06中山、07中京、08京都、09阪神、10小倉
  place = [str(i).zfill(2) for i in range(1, 11)]
  #開催回(MAX10)
  times = [str(i).zfill(2) for i in range(1, 13)]
  #開催日(MAX10)
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
  acquiredIDList = db_instans.get_race_id()
  acquiredIDList = set(acquiredIDList)

  #取得済みのrace_idを除外
  optimization_raceIdList = [i for i in raceIdList if i not in acquiredIDList]

  return optimization_raceIdList


def get_id_prediction():
  # インスタンスの作成&関数の実行
  db_instans = db.Main()
  result_race_id = db_instans.get_race_id()                 # raceテーブルに保存されているrace_idを取得
  acquiredIDList = db_instans.get_prediction_race_id()      # prediction_horseテーブルに保存されているrace_idを取得
  acquiredIDList = set(acquiredIDList)

  #取得済みのrace_idを除外
  optimization_raceIdList = [i for i in result_race_id if i not in acquiredIDList]

  return optimization_raceIdList