from tqdm import tqdm
import component.create_raceID as cr
import component.scrayping_def as sc
import classes.create_scrayping_list as create_list
import classes.scrayping_running_list as running_list
import classes.crontab as cron

insert_instans = create_list.Main()             # インスタンスの作成
running_list_instans = running_list.Main()      # 出馬表をスクレイピングするためのseleniumインスタンスクラス
cron_instans = cron.CrontabControl()            # cronの実行タイミングを設定するインスタンス
exclusionIDList = []                            # 除外race_idリスト
raceIdList = cr.get_id()                        # race_idのリストを生成

raceIdList = [202307040309,202306050301,202306050309,202307040304,202309050312,202306050312,202306050305,202307040311,202307040308,202309050308,202307040305,202306050302,202306050303,202307040306,202309050309,202309050305,202306050304,202307040303,202306050308,202306050311,202307040310,202307040312,202309050301,202309050302,202309050303,202309050304,202309050306,202309050307,202306050307,202307040307,202309050311,202309050310,202306050310,202307040301,202307040302,202306050306]
# raceIdList = [202306050305]

for race_id in tqdm(raceIdList):
  race_id = str(race_id)
  url = f"https://race.netkeiba.com/race/shutuba.html?race_id={race_id}&rf=race_submenu"
  # スクレイピング
  soup = sc.get_soup(url)
  # 各テーブルに対応したデータをinsertする
  insert_instans.insert_plan(soup,race_id)