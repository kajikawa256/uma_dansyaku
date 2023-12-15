from datetime import datetime
import classes.db_operation_class as db
import data.constant as con
import classes.scrayping_running_list as runnningu_list

class Main():
  # コンストラクタ dbのインスタンス作成
  def __init__(self):
    self.db = db.Main()
    self.running = runnningu_list.Main()


  # 過去のレースをデータベースに各テーブルの情報をinsertする
  def insert(self,soup,race_id):
    self.soup = soup
    self.race_id = race_id
    self._insert_race()         # レース情報
    self._insert_result_horse() # 結果情報
    self._insert_hit_detail()   # 払い戻し情報


  # 開催予定の情報をスクレイピングする
  def insert_plan(self,soup,race_id):
    self.soup = soup
    self.race_id = race_id
    self._insert_race()         # レース情報
    self._insert_plan_horse()   # 出馬表


  # AI予想をテーブルにinsertする
  def prediction(self,race_id):
    self.race_id = race_id
    self._insert_prediction()


  #---------- raceテーブル ----------#
  def _insert_race(self):
    race_list = []

    #レース情報のスクレイピング
    RaceData01 = self.soup.find("div",class_="RaceData01").text.replace("\n","").replace(" ","").split("/")
    RaceData02 = self.soup.find("div",class_="RaceData02").text.split()

    # レース前日の出馬表の場合は天気と馬場状態が公開されないため空白で埋める
    if len(RaceData01) > 2:
      weather = RaceData01[2][3:4] if len(RaceData01[2]) <= 4 else RaceData01[2][3:5]                            # 天気
      # 馬場状態
      situation = RaceData01[3] if "障" not in RaceData01[1] else "良"
      if "稍" in situation:
        situation += "重"
      elif "不" in situation:
        situation += "良"
      situation = situation[-1] if ":" in situation[-2:] else situation[-2:]
    else:
      weather = ""
      situation = ""

    title = self.soup.find("div",class_="RaceName").text.replace("\n","")                                      # レースタイトル
    horse_num = RaceData02[-2].replace("頭","")                                                                # 頭数
    race_place = RaceData02[1]                                                                                 # 開催場
    race_num = self.soup.find(class_="RaceNum").text.replace('R',"").replace("\n","")                          # 第何レースか
    spin = "障害" if "障" in RaceData01[1] else RaceData01[1][7:8]                                             # 回り方データの整形
    time = RaceData01[0][0:5]                                                                                  # 発走時刻の整形
    ground = RaceData01[1][0:1] if "障" not in RaceData01[1][0:1] else "障害"                                  # 馬場
    ground = "ダート" if "ダ" in ground else ground                                                            # 馬場2
    distance = RaceData01[1][1:5]                                                                              # 距離
    grade = RaceData02[4]
    if "５００万下" in grade:
      grade = "１勝クラス"
    elif "１０００万下" in grade:
      grade = "２勝クラス"
    elif "１６００万下" in grade:
      grade = "３勝クラス"
    limit = "牝" if "牝" in RaceData02[-4] else "無"                                                           # 制限
    handicap = "定量" if RaceData02[-3] == "馬齢" else RaceData02[-3]                                          # ハンデ

    # 日付の整形
    year = self.race_id[0:4] + "年"
    month_day = self.soup.find("dd",class_="Active").text
    if "/" in month_day:
      month_day = month_day.replace("/","月")
      month_day += "日"
    else:
      month_day = month_day[:-3]
    date_object = datetime.strptime(year + month_day, "%Y年%m月%d日")
    date = date_object.strftime("%Y年%m月%d日")

    # race_list順番(データベース定義書通りの順番)
    order = [
      self.race_id,             # レースID
      title,                    # レース名
      int(race_num),            # レース番号
      int(horse_num),           # 頭数
      ground,                   # 馬場
      spin,                     # 回り方 (障害レースの場合例外が発生する)
      int(distance),            # 距離
      weather,                  # 天気
      situation,                # 馬場状態
      time,                     # 時刻
      date,                     # 日付
      race_place,               # 開催場
      grade,                    # グレード
      limit,                    # 制限
      handicap                  # ハンデ
    ]

    #race_listに格納
    for x in order:
      race_list.append(x)

    # dbにinsertする
    self.db.insert(con.TABLE[con.RACE],race_list)


  #---------- result_horseテーブル(出馬表) ----------#
  def _insert_plan_horse(self):
    # 出馬表をseleniumを使ってスクレイピングする
    resutl_list = self.running.scrayping_running_list(self.race_id)

    # dbにinsertする
    self.db.insert(con.TABLE[con.RESULT_HORSE],resutl_list)


  #---------- result_horseテーブル ----------#
  def _insert_result_horse(self):
    result_list = []

    # 馬テーブルを行ごとに分解してリスト化
    table = self.soup.find("table",summary="全着順")
    colomuns = table.find_all("tr")

   # result_listに格納
    for colomu in colomuns:
      if colomu == colomuns[0]:
        # 0行目はカラム名のためスキップ
        continue

      # カラムをデータに分解
      datas = colomu.text.split()

      if colomu == colomuns[1]:
        # 1着の馬は着差が空白になり、indexが１つ少なくなるので空白を挿入
        datas.insert(8,"")

      ranking = datas[0]
      horseflame = datas[1]
      horsenum = datas[2]
      hname = datas[3]
      horse_id = colomu.find(class_="Horse_Info").find("a").get("href").split("horse/")[1]
      gender = datas[4][:1]
      age = datas[4].replace(datas[4][:1],"")
      weight = float(datas[5])
      jockey = datas[6]
      jockey_id = colomu.find(class_="Jockey").find("a").get("href").split("recent/")[1].replace("/","")
      trainer = colomu.find(class_="Trainer").find("a").text
      trainer_id = colomu.find(class_="Trainer").find("a").get("href").split("recent/")[1].replace("/","")
      base = colomu.find(class_="Trainer").find("span").text
      horse_weight = colomu.find(class_="Weight").text[1:4]
      weight_gain_loss = colomu.find(class_="Weight").find("small").text
      weight_gain_loss = weight_gain_loss.replace("(","").replace(")","") if weight_gain_loss != "" else 0
      weight_gain_loss = int(weight_gain_loss)

      if datas[0] == "除外":
        popular = 0
        odds = 0
        if horse_weight == "":
          horse_weight = 0
      elif datas[0] == "取消":
        horse_weight = 0
        popular = 0
        odds = 0
      elif datas[0] == "中止":
        popular = int(datas[7])
        odds = float(datas[8])
      else:
        try:
          popular = int(datas[9])
        except:
          print(self.race_id)
        odds = float(datas[10])

      # 順にresult_listに格納
      result_list = [
        self.race_id,     # レースID
        ranking,          # 着順
        int(horseflame),  # 馬枠
        int(horsenum),    # 馬番
        hname,            # 馬名
        horse_id,         # horseid
        gender,           # 性別
        age,              # 年齢
        weight,           # 斤量
        jockey,           # 騎手
        jockey_id,        # 騎手ID
        trainer,          # 調教師
        trainer_id,       # 調教師ID
        base,             # 拠点
        horse_weight,     # 馬体重
        weight_gain_loss, # 体重増減
        odds,             # オッズ
        popular           # 人気
      ]

      # dbにinsertする
      self.db.insert(con.TABLE[con.RESULT_HORSE],result_list)


  #---------- PREDICTION_HORSEテーブル ----------#
  def _insert_prediction(self):
    prediction_list = []

    # レースIDから出馬表とレース表を取得しdfにする

    # 出馬表をdfで渡すとprediction_listを返してくれる関数を呼び出す

    # dbにinsertする
    self.db.insert(con.TABLE[con.PREDICTION_HORSE],prediction_list)


  #---------- hit_detailテーブル ----------#
  def _insert_hit_detail(self):
    pay_list = []
    trs = []

    # 払い戻しテーブルを取得し、行ごとに分解、リスト化
    tables= self.soup.find("div", class_="FullWrap").find_all("table")
    for table in tables:
      trs += table.find_all("tr")

    # 行ごとにループ
    for tr in trs:
      kinds = tr.find("th").text   # 式別
      datas = tr.find_all("td")    # 整形前のデータ

      split_nums = datas[0].text.replace("\n\n\n\n","").split()    # 馬番または馬枠
      # 各式別によってindex値を設定
      if kinds in ['枠連', '馬連', 'ワイド', '馬単']:
        index = 2
      elif kinds in ['3連複', '3連単']:
        index = 3
      else:
        index = 1
      # 連番を一括りにする
      nums = [split_nums[i:i+index] for i in range(0, len(split_nums), index)]

      bet_backs = datas[1].get_text("</br>").split("</br>")            # 払い戻し額
      populars_test = datas[2].get_text("</br>").split("</br>")        # 何番人気
      populars = [item for item in populars_test if "\n" not in item]  # リストに改行コードが含まれていたら削除

      # 式別ごとにデータをリスト化
      for num,bet_back,popular in zip(nums,bet_backs,populars):
        delimiter = ">"  if "単" in kinds else "-"  # 連単の場合 > で区切る

        sublist = [
          self.race_id,
          kinds,
          delimiter.join(num),
          bet_back.replace("円",""),
          popular.replace("人気","").replace(",","")
        ]

        # ワイドの場合馬番にあたるindexの部分に空白、それ以外の場合は枠番にあたるindexの部分に空白を挿入
        index = 2 if kinds == "ワイド" else 3
        sublist.insert(index,"")
        pay_list.extend(sublist)

    # dbにinsertする
    self.db.insert(con.TABLE[con.HIT_DETAIL],pay_list)


