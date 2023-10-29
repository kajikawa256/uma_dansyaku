import re
from datetime import datetime
import classes.db_operation_class as db
import data.constant as con
import component.count_horse_num as count

class Main():
  # コンストラクタ dbのインスタンス作成
  def __init__(self):
    self.db = db.Main()


  # データベースに各テーブルの情報をinsertする
  def insert(self,soup,race_id):
    self.soup = soup
    self.race_id = race_id
    # self._insert_race()
    self._insert_result_horse()
    self._insert_hit_detail()


  # raceテーブル
  def _insert_race(self):
    race_list = []

    #レース情報のスクレイピング
    title = self.soup.find_all("h1")
    horse_num = count.get(self.soup)                                                 #頭数
    race_info = re.sub(r"[/+ ]","", self.soup.find("diary_snap_cut").text).split()   #レース情報
    race_info =  race_info if "過去" not in race_info[-1] else race_info[:-1]
    race_info2 = self.soup.find("p",class_="smalltxt").text.split()                  #レース情報2
    race_num = self.soup.find("dt").text.replace('R','')                             #レース番号
    race_place = self.soup.find("a",class_="active").text                            #開催場

    # 日付の整形
    date_object = datetime.strptime(race_info2[0], "%Y年%m月%d日")
    # 新しいフォーマットの日付文字列を生成
    date = date_object.strftime("%Y年%m月%d日")
    # 回り方データの整形
    spin = "" if "障害" in race_info2[2] else race_info[0][1:2]
    # 時刻の整形
    time = race_info[-1][3:8] 
    # 天気
    weather = race_info[1][3:4] if len(race_info[1]) <= 4 else race_info[1][3:5]
    # 馬場状態
    situation = race_info[2] if "障" not in race_info[0][0:1] else ""
    situation = situation[-1] if ":" in situation[-2:] else situation[-2:]
    # 馬場
    ground = race_info[0][0:1] if "障" not in race_info[0][0:1] else ""

    #race_list順番(データベース定義書通りの順番)
    order = [
      self.race_id,                  #レースID
      date,                     #日付
      title[1].text,            #レース名
      int(race_num),            #レース番号
      time,                     #時刻
      int(race_info[0][-5:-1]), #距離
      horse_num,                #頭数
      ground,                   #馬場
      race_place,               #開催場
      weather,                  #天気
      spin,                     #回り方 (障害レースの場合例外が発生する)
      situation                 #馬場状態
    ]

    #race_listに格納
    for x in order:
      race_list.append(x)

    # dbにinsertする
    self.db.insert(con.TABLE[con.RACE],race_list)

    
  # result_horseテーブル
  def _insert_result_horse(self):
    result_list = []

    # 馬テーブルを行ごとに分解してリスト化
    table = self.soup.find("table",class_="race_table_01 nk_tb_common")
    colomuns = table.find_all("tr")

    # result_listに格納(上位５馬のみ)
    for x in range(1,6):
      datas = colomuns[x].text.split()
      
      # レースID、馬名、馬番、馬枠、着順の順にresult_listに格納
      result_list = [
        self.race_id,
        datas[3],
        int(datas[2]),
        int(datas[1]),
        int(datas[0][0:1])
      ]
      
      # dbにinsertする
      self.db.insert(con.TABLE[con.RESULT_HORSE],result_list)


  # hit_detailテーブル
  def _insert_hit_detail(self):
    pay_list = []
    
    # 払い戻しテーブルを取得し、行ごとに分解、リスト化
    table = self.soup.find("dd",class_="fc")
    rows = table.find_all("tr")
    
    # 行ごとにループ
    for tr in rows:
      kinds = tr.find("th").text                               # 式別
      datas = tr.find_all("td")                                # データ
      nums = datas[0].get_text("</br>").split("</br>")         # 馬番または馬枠
      bet_backs = datas[1].get_text("</br>").split("</br>")    # 払い戻し額
      populars = datas[2].get_text("</br>").split("</br>")     # 何番人気

      # 式別ごとにデータをリスト化
      for num,bet_back,popular in zip(nums,bet_backs,populars):
        sublist = [
          self.race_id,
          kinds,
          num.replace(" ",""),
          bet_back,
          popular,
        ]
        
        # ワイドの場合馬番にあたるindexの部分に空白、それ以外の場合は枠番にあたるindexの部分に空白を挿入
        index = 2 if kinds == "ワイド" else 3
        sublist.insert(index,"")
        pay_list.extend(sublist)

    # dbにinsertする
    self.db.insert(con.TABLE[con.HIT_DETAIL],pay_list)
              

