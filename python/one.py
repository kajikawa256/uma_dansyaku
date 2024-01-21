import classes.predict as predict
import time
from tqdm import tqdm
import component.create_raceID as cr
import component.create_escape_list as escape
import component.day_check as get_day
import component.scrayping_def as sc
import classes.create_scrayping_list as create_list
import classes.scrayping_running_list as running_list
import classes.crontab as cron
import classes.predict as predict

#任意の年数分スクレイピングしてデータベースにinsertする
insert_instans = create_list.Main()             # インスタンスの作成
predict_instans = predict.Main()
running_list_instans = running_list.Main()      # 出馬表をスクレイピングするためのseleniumインスタンスクラス
cron_instans = cron.CrontabControl()            # cronの実行タイミングを設定するインスタンス
exclusionIDList = []                            # 除外race_idリスト
raceIdList = cr.get_id()                        # race_idのリストを生成
predict_instans = predict.Main()

race_id = "202406010604"


# レースの日付が今日以降の場合出馬表のスクレイピング実行
url = f"https://race.netkeiba.com/race/shutuba.html?race_id={race_id}&rf=race_submenu"
# スクレイピング
soup = sc.get_soup(url)
# 各テーブルに対応したデータをinsertする
insert_instans.insert_plan(soup,race_id)
predict_instans.predict(race_id)