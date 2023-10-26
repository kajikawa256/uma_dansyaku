#呼び出すときにBeautifulSoupで整形したhtmlとレースID引数として渡す
import component.count_horse_num as count
import component.count_paylist_index as index_count
import component.connect_words as cw
import db.insert as insert
import data.constant as con


#レースの払い戻し結果を返す関数
def insert_hit_detail(soup,race_id):
  pay_list = []
  
  #払い戻しテーブルを取得し、行ごとに分解、リスト化
  table = soup.find("dd",class_="fc")
  rows = table.find_all("tr")
  
  # それぞれの識別に対応したデータの整形しpay_listに格納
  for data in rows:
    #変数宣言

    sublist = []
    data_list = []

    # hit_detailテーブルへinsertする情報を作成
    data_list.append(race_id)
    x = data.get_text(separator=" ").replace(",","").split()
    data_list.extend(x)
    
    # レコード数取得
    index = index_count.get_index(data_list)

    # 区切る位置取得
    separator = index_count.get_separator(data_list)

    # データの整形
    data_list = cw.connect(data_list,index,separator)

    for j in range(index):
      sublist = data_list[0:2]
      for i in range(3):
        sublist.append(data_list[2 + i*index + j ])
      insert_index = 2 if data_list[1] == "枠連" else 3
      sublist.insert(insert_index,"")
      pay_list.extend(sublist)

  insert.insert(con.TABLE[con.HIT_DETAIL],pay_list)
            

