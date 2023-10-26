#呼び出すときにBeautifulSoupで整形したhtmlとレースID引数として渡す
import re
import function.count_horse_num as count
import db.insert as insert
import constant as con

def insert_race(soup,race_id):
  race_list = []

  #レース情報のスクレイピング
  horse_num = count.get(soup)                                                 #頭数
  race_info = re.sub(r"[/+ ]","", soup.find("diary_snap_cut").text).split()   #レース情報
  race_info2 = soup.find("p",class_="smalltxt").text.split()                  #レース情報2
  race_num = soup.find("dt").text.replace('R','')                             #レース番号
  race_place = soup.find("a",class_="active").text                            #開催場

  #race_list順番(データベース定義書通りの順番)
  order = [
    race_id,                #レースID
    race_info2[0],          #日付
    race_info2[2],          #レース名
    int(race_num),          #レース番号
    race_info[3][3:8],      #時刻
    int(race_info[0][2:6]), #距離
    horse_num,              #頭数
    race_info[0][0:1],      #馬場
    race_place,             #開催場
    race_info[1][3:4],      #天気
    ("" if "障害" in race_info[2] else race_info[0][1:2]),      #回り方 (障害レースの場合直線)
    race_info[2][-1]        #馬場状態
  ]

  

  #race_listに格納
  for x in order:
    race_list.append(x)
  
  insert.insert(con.TABLE[con.RACE],race_list)