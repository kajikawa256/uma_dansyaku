#呼び出すときにBeautifulSoupで整形したhtmlとレースID引数として渡す

#レースの払い戻し結果を返す関数
def get(soup,race_id):
  #return用のリスト
  pay_list = []

  #出馬数取得し変数horse_numに格納
  table = soup.find("table",class_="race_table_01 nk_tb_common")
  colomuns = table.find_all("tr")
  horse_num = len(colomuns) - 1
  
  #払い戻しテーブルを取得し、行ごとに分解、リスト化
  table = soup.find("dd",class_="fc")
  rows = table.find_all("tr")
  
  # それぞれの識別に対応したデータの整形しpay_listに格納
  for data in rows:
    data_list = []
    data_list.append(race_id)
    x = data.get_text(separator=" ").replace(",","").split()
    data_list.extend(x)

    if(data_list[1] == "単勝"):
      data_list.insert(3,"")
      pay_list.extend(data_list)

    elif(data_list[1] == "複勝"):
      #出馬頭数が7以下の場合は複勝は２着までが的中となる
      index = 3 if horse_num > 7 else 2
      for i in range(index):
          sublist = data_list[0:2] + [data_list[j] for j in range(2 + i, len(data_list), index)]
          sublist.insert(3, "")
          pay_list.extend(sublist)

    elif(data_list[1] == "枠連"):
      num = "".join(data_list[2:5])
      data_list[2:5] = []
      data_list.insert(2,"")
      data_list.insert(3,num)
      pay_list.extend(data_list)
      
    elif(data_list[1] == "馬連" or data_list[1] == "馬単"):
      num = "".join(data_list[2:5])
      data_list[2:5] = []
      data_list.insert(2,num)
      data_list.insert(3,"")
      pay_list.extend(data_list)

    elif(data_list[1] == "ワイド"):
      num = [data_list[i:i+3] for i in range(2,len(data_list),3)]
      data_list[2:11] = []
      for i in range(3):
        data_list.insert(i+2,"".join(num[i]))
      for i in range(index):
          sublist = data_list[0:2] + [data_list[j] for j in range(2 + i, len(data_list), 3)]
          sublist.insert(3, "")
          pay_list.extend(sublist)

    elif(data_list[1] == "三連複" or data_list[1] == "三連単"):
      num = "".join(data_list[2:7])
      data_list[2:7] = []
      data_list.insert(2,num)
      data_list.insert(3,"")
      pay_list.extend(data_list)
    
    else:
      #例外処理
      print(f"例外発生：{data_list}")

  return pay_list
            

