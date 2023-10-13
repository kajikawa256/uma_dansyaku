#呼び出すときにBeautifulSoupで整形したhtmlとレースID引数として渡す
import constant as con

#全馬の全データを返す関数
def get(soup,race_id):
  #return用のリスト
  result_list = []

  result_list.append(race_id)
  table = soup.find_all("tr",class_="HorseList")

  #race_listに格納
  for x in table:
    print(x.text.split())
  
  return result_list


#上位５馬の馬枠と馬番と馬名を返す関数
def get_only(soup,race_id):
  result_list = []

  result_list.append(race_id)
  table = soup.find_all("tr",class_="HorseList")

  #race_listに格納
  for x in range(5):
    datas = table[x].text.split()
    
    #馬名、馬番、馬枠の順にresult_listに格納
    result_list.append(datas[con.HORSE_NAME])
    result_list.append(datas[con.HORSE_NUM])
    result_list.append(datas[con.HORSE_FRAME])
    result_list.append(datas[con.HORSE_ARRIVAL])
  
  return result_list
