import requests
from bs4 import BeautifulSoup
import re
import scrayping
import time
from datetime import datetime, timedelta
import constant as con

def scrayping():
  #現在の日付を取得
  current_date = datetime.now()

  #変数宣言
  year = "2023"
  race_id_list = [[[]]]  #[開催場,開催回,何日目][レース番号][馬番(着順)]
  race_id = ""
  error = "true"
  result_list = []
  result_ranking = {}

  #指定した年から現在の日付までの全レースを取得
  for where in con.race_venue_list:
    error = "true"
    for howtime in range(con.HOWTIME):
      for howdays in range(con.HOWDAYS):
        for race_num in range(con.RACENUM):
          #文字に変換
          ht = str(howtime+1)
          hd = str(howdays+1)
          rn = str(race_num+1)
          race_id = year + where + ('0' + ht if len(ht) != 2 else ht) + ('0' + hd if len(hd) != 2 else hd) + ('0' + rn if len(rn) != 2 else rn)

          #URLを作成
          url = f"https://race.netkeiba.com/race/result.html?race_id={race_id}&rf=race_list"

          #スクレイピング
          res = requests.get(url)
          res.encoding = "EUC-JP"
          soup = BeautifulSoup(res.text, "html.parser")
          list = soup.find_all("table")
          title = soup.find_all("title")

          #正常にスクレイピングが行われた場合
          if list != []:
            list = list[0].text
            list = list.split()
            title = title[0].text
            #1秒待つ
            time.sleep(0.1)

            # #上位５馬とレースタイトル表示
            z = 14
            for x in range (5):
              for y in range (2):
                if x == 0:
                  # print(f"{list[3 + z + y]}",end=' ')
                  result_list.append(list[3 + z + y])
                else:
                  # print(f"{list[2 + z + y]}",end=' ')
                  result_list.append(list[2 + z + y])
              z+=15

            #改行
            # print()
            # print()

            result_ranking[race_id] = result_list

            # print(result_ranking)
            # print(result_list)

          else:
            error = "false"
            break
          result_list = []

        if error == "false" :
          break
        return result_ranking
      if error == "false" :
        break
    

