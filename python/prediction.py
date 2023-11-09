import classes.db_operation_class as db
import pandas as pd
import re
import numpy as np
import requests
from bs4 import BeautifulSoup as bs

# db_instans = db.Main()                # インスタンスの作成
# raceId = db_instans.get_race_id() # raceテーブルに保存されているidを取得しリスト化


def create_df (raceId):
  url = f"https://race.netkeiba.com/race/shutuba.html?race_id={raceId}"
  res = requests.get(url)
  res.encoding = ""
  soup = bs(res.text, "html.parser")

  # db_instans.insert()

  columns = [
    'raceId','枠番','馬番','馬名','horseId','性','年齢',
  '斤量','騎手','jockeyId','調教師','trainerId','拠点',
  '馬体重','体重増減','単勝','人気'
    ]
    
  table = soup.find_all('table')[0]
  tbody = table.find('tbody')
  tr = tbody.find_all('tr')[0]
  idx = 1
  dicList = []
  for idx, tr in enumerate(tbody.find_all('tr')):
      try:    
          dic = {}
          dic['raceId'] = raceId
          print("aaa")
          dic['枠番'] = tr.find('td',class_=re.compile('Waku')).text
          dic['馬番'] = tr.find('td',class_=re.compile('Umaban')).text
          horseInfo = tr.find('td',class_='HorseInfo')
          dic['馬名'] = horseInfo.text.replace('\n','')
          dic['horseId'] = str(horseInfo.find('a')).split('/horse/')[1].split('"')[0]
          dic['性'] = tr.find('td',class_='Barei').text[0]
          dic['年齢'] = dic['性'] = tr.find('td',class_='Barei').text[1]
          dic['斤量'] = tr.find_all('td',class_='Txt_C').text
          jockey = tr.find('td',class_='Jockey')
          dic['騎手'] = jockey.text.replace('\n','')
          dic['jockeyId'] = str(jockey.find('a')).text[0]
          trainer = tr.find('td',class_='Trainer')
          anker = trainer.find('a')
          dic['調教師'] = anker.text
          dic['trainerId'] = str(anker).split('/recent/')[1].split('/')[0]
          span = trainer.find('span')
          dic['拠点'] = '東' if '美浦' in span.text else \
                          '西' if '栗東' in span.text else \
                              '他' if '他' in span.text else '外'
          dic['単勝'] = tr.find('td', class_='Popular').text
          dic['人気'] = tr.find('td', class_='Popular_NinKi').find('span').text
      except:
          pass
      try:
          weight = tr.find('td',class_='Weight')
          tmp = weight.text.replace('\n','').split('(')[0]
          dic['馬体重'] = tmp[0]
          dic['体重増減'] = tmp[1].split(')')
      except:
          dic['馬体重'] = np.nan
          dic['体重増減'] = np.nan
      dicList.append(dic)
  entryDf = pd.DataFrame(dicList)

  print(entryDf.columns)
