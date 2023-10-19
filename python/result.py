import json
import scrayping as sc

print(json.dumps(sc.scrayping()))

# for x in range(9):
#   print() #レースID #日付 #レース名 #時刻 #距離 #頭数 #馬場(芝かだーとか) #開催地 #天気
#   print() #馬番、馬名

# とりあえず札幌の１開催分（６日目まで）
print(json.dumps(sc.scrayping()))