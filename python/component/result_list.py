#呼び出すときにBeautifulSoupで整形したhtmlを引数として渡す
import constant as con

#全馬の全データを返す関数
def get(soup):
  #return用のリスト
  result_list = []

  table = soup.find_all("tr",class_="HorseList")

  #race_listに格納
  for x in table:
    print(x.text.split())
  
  return result_list


#上位５馬の馬枠と馬番と馬名を返す関数
def get_only(soup):
  result_list = []

  table = soup.find_all("tr",class_="HorseList")

  #race_listに格納
  for x in range(5):
    datas = table[x].text.split()
    
    #馬枠、馬番、馬名の順にresult_listに格納
    result_list.append(datas[con.HORSE_FRAME])
    result_list.append(datas[con.HORSE_NUM])
    result_list.append(datas[con.HORSE_NAME])
  
  return result_list
