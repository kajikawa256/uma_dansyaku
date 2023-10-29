#各データのindex値
HORSE_ARRIVAL = 0
HORSE_FRAME = 1
HORSE_NUM = 2
HORSE_NAME = 3

'''
-----------------------以下データベース-----------------------
'''
#データベース接続情報
USER = "umadan"
PASSWORD = "umadan"
HOST = "localhost"
DB = "umadan"

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