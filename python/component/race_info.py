#呼び出すときにBeautifulSoupで整形したhtmlとレースID引数として渡す
import re
def get(soup,race_id):
  #return用のリスト
  race_list = []

  #レース情報のスクレイピング
  table = soup.find("table",class_="race_table_01 nk_tb_common")
  colomuns = table.find_all("tr")
  horse_num = len(colomuns) - 1
  race_info = re.sub(r"[/+ ]","", soup.find("diary_snap_cut").text).split()
  race_info2 = soup.find("p",class_="smalltxt").text.split()
  race_num = soup.find("dt").text.replace('R','')             #レース番号
  race_place = soup.find("a",class_="active").text            #開催上

  #レース情報のスクレイピング 旧バージョン
  # racename = soup.find("div",class_="RaceName").text.split()  #レース名
  # race_num = soup.find("div",class_="RaceNumWrap")            #レース番号
  # race_num = race_num.find("li",class_="Active").text.split()
  # rd01 = soup.find("div",class_="RaceData01").text            #発走時刻,馬場(種類),距離,[右回り、左回り],天気,馬場状態
  # rd01 = re.split('[/\n]',rd01)                               #['', '09:50発走 ', ' 芝1200m (右\xa0A)', '', ' 天候:晴', '', ' 馬場:良', '']
  # rd02 = soup.find("div",class_="RaceData02").text.split()    #第何回,開催場,何日目,[],[],[],[],何頭,[]
  # day = soup.find("dd",class_="Active").text                  #x月x日(x曜日)
  
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
    race_info[0][1:2],      #回り方
    race_info[2][-1]        #馬場状態
  ]

  #race_listに格納
  for x in order:
    race_list.append(x)
  
  return race_list