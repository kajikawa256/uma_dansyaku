#レースに出馬する馬の数をカウントして値を返す関数

def get(soup):
  table = soup.find("table",class_="race_table_01 nk_tb_common")              
  colomuns = table.find_all("tr")
  horse_num = len(colomuns) - 1

  return horse_num


def get_update(soup):
  table = soup.find("table",summary="全着順")              
  colomuns = table.find_all("tr")
  horse_num = len(colomuns) - 1

  return horse_num