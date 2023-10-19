import json
import scrayping
print(json.dumps(scrayping.scrayping()))

# for x in range(9):
#   print() #レースID #日付 #レース名 #時刻 #距離 #頭数 #馬場(芝かだーとか) #開催地 #天気
#   print() #馬番、馬名



# とりあえず12レース分の情報のみを取得（phpとデータベースデバッグ用）
print(json.dumps(sc.scrayping()))