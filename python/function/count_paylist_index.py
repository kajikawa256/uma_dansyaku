# 同着が発生したレースの払い戻しのレコード数に例外があるためレコード数をカウントして返す関数

def get_index(data_list:list):
  index = 0
  separator = 0

  # 割る数をテーブルによって変更
  if "ワイド" in data_list or "馬連" in data_list or "馬単" in data_list or "枠連" in data_list:
    separator = 5
  elif "三連複" in data_list or "三連単" in data_list:
    separator = 7
  else:
    separator = 3
  
  # index値生成
  check_list = data_list.copy()
  del check_list[0:2]
  index = int(len(data_list) / separator)

  return index


# 各式別の分割地点のindexを返す
def get_separator(data_list:list):
  separator = 0

  # 割る数をテーブルによって変更
  if "ワイド" in data_list or "馬連" in data_list or "馬単" in data_list or "枠連" in data_list:
    separator = 5
  elif "三連複" in data_list or "三連単" in data_list:
    separator = 7
  else:
    separator = 3
  
  return separator