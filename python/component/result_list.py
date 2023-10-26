#呼び出すときにBeautifulSoupで整形したhtmlとレースID引数として渡す
import constant as con

#上位５馬の馬枠と馬番と馬名,着順を返す関数
def get_only(soup,race_id):
  result_list = []

  # 馬テーブルを行ごとに分解してリスト化
  table = soup.find("table",class_="race_table_01 nk_tb_common")
  colomuns = table.find_all("tr")

  #race_listに格納
  for x in range(1,6):
    datas = colomuns[x].text.split()
    
    # レースID、馬名、馬番、馬枠、着順の順にresult_listに格納
    result_list.append(race_id)
    result_list.append(datas[con.HORSE_NAME])
    result_list.append(int(datas[con.HORSE_NUM]))
    result_list.append(int(datas[con.HORSE_FRAME]))
    result_list.append(int(datas[con.HORSE_ARRIVAL]))

  return result_list