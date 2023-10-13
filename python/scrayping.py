import requests
from bs4 import BeautifulSoup
import time
import tqdm
import constant as con
import component.result_list as rl
import component.race_info as ri
import component.pay_list as pl


#変数宣言
year = "2023"        #任意の年数
race_id = ""         #宣言
error = "true"       #エラーフラグ
result_list = []     #result_rankingの子要素
race_list = []       #race_infoの子要素
pay_list = []        #result_payの子要素
result_ranking = {}  #total_infoの子要素
race_info = {}       #total_infoの子要素
resulut_pay = {}     #total_infoの子要素
total_info = []      #すべてまとめたリスト

def scrayping():

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

          #1桁の数字を0付きの2桁の数字に変換
          race_id = year + where + ('0' + ht if len(ht) != 2 else ht) + ('0' + hd if len(hd) != 2 else hd) + ('0' + rn if len(rn) != 2 else rn)

          #URLを作成
          url = f"https://race.netkeiba.com/race/result.html?race_id={race_id}&rf=race_list"

          #スクレイピング
          res = requests.get(url)
          res.encoding = "EUC-JP"
          soup = BeautifulSoup(res.text, "html.parser")
          
          list = soup.find_all("table")

          #正常にスクレイピングが行われた場合（ここの条件式は変更の余地あり）
          if list != []:

            result_list = rl.get_only(soup) #上位５馬の馬枠、馬番、馬名を取得しresult_listに格納
            race_list = ri.get(soup)        #レース情報を取得しrace_listに格納
            pay_list = pl.get(soup)         #払い戻し情報を取得しpay_listに格納

            #1秒待つ
            # time.sleep(1)

            #各辞書型の配列にリストを格納する
            result_ranking[race_id] = result_list
            race_info[race_id] = race_list
            resulut_pay[race_id] = pay_list

            total_info.append(result_ranking)
            total_info.append(race_info)
            total_info.append(resulut_pay)

          else:
            error = "false"
            break
          
          #親要素に追加したら初期化
          result_list = []
          race_list = []
          pay_list = []
        
        total_info.append(result_ranking)
        total_info.append(race_info)
        total_info.append(resulut_pay)

        return(total_info)
        if error == "false" :
          break
      if error == "false" :
        break

  #スクレイピングしたデータをすべてtotal_infoに格納
  total_info.append(result_ranking)
  total_info.append(race_info)
  total_info.append(resulut_pay)

  return(total_info)



