    # ['202301020112', '馬単', '11', '→', '6', '4110', '13'] のようなリストを ['202301020112', '馬単', '11→6', '', '4110', '13']のように繋げる関数

def connect(data_list:list,index,separator):
    horseNumber = []

    for i in range(index):
      horseNumber.append("".join(data_list[2:separator]))
      data_list[2:separator] = []
    for i in range(index):
      data_list.insert(2,horseNumber[index - (i+1)])

    return data_list