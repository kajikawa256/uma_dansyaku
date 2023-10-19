#呼び出すときにBeautifulSoupで整形したhtmlとレースID引数として渡す
import re

def get(soup,race_id):
  #return用のリスト
  race_list = []

  #レース情報のスクレイピング
  racename = soup.find("div",class_="RaceName").text.split()  #レース名
  race_num = soup.find("div",class_="RaceNumWrap")            #レース番号
  race_num = race_num.find("li",class_="Active").text.split()
  rd01 = soup.find("div",class_="RaceData01").text            #発走時刻,馬場(種類),距離,[右回り、左回り],天気,馬場状態
  rd01 = re.split('[/\n]',rd01)                               #['', '09:50発走 ', ' 芝1200m (右\xa0A)', '', ' 天候:晴', '', ' 馬場:良', '']
  rd02 = soup.find("div",class_="RaceData02").text.split()    #第何回,開催場,何日目,[],[],[],[],何頭,[]
  day = soup.find("dd",class_="Active").text                  #x月x日(x曜日)
  
  #race_list順番(データベース定義書通りの順番)
  order = [
    race_id,      #レースID
    day,          #日付
    racename[0],  #レース名
    int(race_num[0].replace('R','')),  #レース番号
    rd01[1][0:5], #時刻
    int(rd01[2][2:6]), #距離
    int(rd02[7].replace('頭','')),      #頭数
    rd01[2][1:2], #馬場
    rd02[1],      #開催場
    rd01[4][4:5], #天気
    rd01[2][9:10],#回り方
    rd01[6][4:5]  #馬場状態
  ]

  #race_listに格納
  for x in order:
    race_list.append(x)
  
  return race_list