#呼び出すときにBeautifulSoupで整形したhtmlを引数として渡す

#レースの払い戻し結果を返す関数
def get(soup):
  #return用のリスト
  pay_list = []

  tables = soup.find_all("table",class_="Payout_Detail_Table")
  
  #pay_listに格納
  for table in tables:
    table_text = table.text.strip().split()
    pay_list.extend(table_text) #リスト同士を結合

  return pay_list
            

