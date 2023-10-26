#馬の頭数をカウントして値を返す関数

def get(soup):
  table = soup.find("table",class_="race_table_01 nk_tb_common")              
  colomuns = table.find_all("tr")
  horse_num = len(colomuns) - 1
  

  return horse_num