'''
-----------------------以下データベース-----------------------
'''
#データベース接続情報
# USER = "uma_python"
# PASSWORD = "cog_dwxpyt"
# HOST = "umadan-db.cg4rvknxvwgf.us-east-2.rds.amazonaws.com" #その時のIPアドレス（AWS上では多分変わる）
USER = "root"
PASSWORD = "root"
HOST = "localhost" #その時のIPアドレス（AWS上では多分変わる）
DB = "umadan"
PORT = "3306"

# テーブル情報をまとめたリスト
TABLE = ['HIT','RACE','RESULT_HORSE','PREDICTION_HORSE','HIT_DETAIL','PRIZE_MONEY']
COLUMNS = [
    ['RACEDATE','COLLECT_PR','HIT_PR'],
    ['RACE_ID','RNAME','RACENUMBER','HORSE_TOTAL','GROUND','SPIN','DISTANCE','WEATHER','SITUATION','TIME','RACEDATE','PLACE','GRADE','LIMIT','HANDICAP','RACE_TYPE'],
    ['RACE_ID', 'HORSEFRAME','HORSENUMBER','HNAME','HORSE_ID','GENDER','AGE','WEIGHT','JOCKEY','JOCKEY_ID','TORAINER','TORAINER_ID','BASE','HORSE_WEIGHT','WEIGHT_GAIN_LOSS','ODDS','POPULAR','RANKING','TIME'],
    ['RACE_ID', 'HNAME', 'HORSENUMBER', 'HORSEFRAME', 'RANKING'],
    ['RACE_ID','KINDS','HORSENUMBER','HORSEFRAME','BETBACK','POPULAR'],
    ['RACE_ID','RANKING','PRIZE']
]

# テーブルを明示的に指定できるようにした定数
HIT = 0
RACE = 1
RESULT_HORSE = 2
PREDICTION_HORSE = 3
HIT_DETAIL = 4
PRIZE_MONEY = 5