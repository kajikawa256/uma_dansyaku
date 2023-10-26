HOWTIME = 36      #開催回数
HOWDAYS = 12      #１開催につき何日目
RACENUM = 12      #１日何レースか

#各データのindex値
HORSE_ARRIVAL = 0
HORSE_FRAME = 1
HORSE_NUM = 2
HORSE_NAME = 3

#開催場コード
SAPORO = "01"     #札幌
HAKODATE = "02"   #函館
HUKUSHIMA = "03"  #福島
NIGATA = "04"     #新潟
TOKYO = "05"      #東京
NAKAYAMA = "06"   #中山
TYUKYO = "07"     #中京
KYOTO = "08"      #京都
HANSHIN = "09"    #阪神
OGURA = "10"      #小倉

#上記のコードをまとめたリスト
race_venue_list = [
  SAPORO,
  HAKODATE,
  HUKUSHIMA,
  NIGATA,
  TOKYO,
  NAKAYAMA,
  TYUKYO,
  KYOTO,
  HANSHIN,
  OGURA
]


'''
-----------------------以下データベース-----------------------
'''

# テーブル情報をまとめたリスト
TABLE = ['HIT','RACE','RESULT_HORSE','PREDICTION_HORSE','HIT_DETAIL']
COLUMNS = [
    ['RACEDATE','COLLECT_PR','HIT_PR'],
    ['RACE_ID', 'RACEDATE','RNAME','RACENUMBER','TIME','DISTANCE','HORSE_TOTAL','GROUND','PLACE','WEATHER','SPIN','SITUATION'],
    ['RACE_ID', 'HNAME', 'HORSENUMBER', 'HORSEFRAME', 'RANKING'],
    ['RACE_ID', 'HNAME', 'HORSENUMBER', 'HORSEFRAME', 'RANKING'],
    ['RACE_ID','KINDS','HORSENUMBER','HORSEFRAME','BETBACK','POPULAR']
]

# テーブルを明示的に指定できるようにした定数
HIT = 0
RACE = 1
RESULT_HORSE = 2
PREDICTION_HORSE = 3
HIT_DETAIL = 4