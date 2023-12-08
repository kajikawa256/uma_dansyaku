# 毎日8時に自動実行するファイル

from tqdm import tqdm
import datetime
import classes.crontab as cron
import classes.db_operation_class as db
import component.create_raceID as cr


raceIdList = cr.update_race_id()        # 結果確定していないレースIDの取得
db_instans = db.Main()                  # dbインスタンス
cron_instans = cron.CrontabControl()    # cronインスタンス

command_first = 'python ./cron/confirm_info.py >> ../data/output.txt'
command_second = 'python ./cron/just_before.py >> ../data/output.txt'
schedule = 'm h * * *'

for race_id in tqdm(raceIdList):
  # レースの発走時間を取得
  starting_time = db_instans.get_starting_time(race_id)
  dt1 = datetime.datetime.strptime(starting_time, '%H:%M')
  first_time = dt1 + datetime.timedelta(minutes=-30) # 発走時間の30分前
  second_time = dt1 + datetime.timedelta(minutes=+20) # 発走時間の20分後
  
  # 発走時刻の時間と分を設定
  # 30分前
  schedule1 = schedule.replace("m",str(first_time.minute))
  schedule1 = schedule1.replace("h",str(first_time.hour))
  # 20分後
  schedule2 = schedule.replace("m",str(second_time.minute))
  schedule2 = schedule2.replace("h",str(second_time.hour))

  # 書き込み
  cron_instans.write_job(command_first, schedule1)
  cron_instans.write_job(command_second, schedule2)


# タスクスケジュールの監視を開始
cron_instans.monitor_start(len(raceIdList)*2)