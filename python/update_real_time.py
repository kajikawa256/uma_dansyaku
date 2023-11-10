import classes.db_operation_class as db

db_instans = db.Main()
race_id = db_instans.get_need_update_race_id()

# 30分に1度実行するプログラム
db_instans.race_update(race_id,"晴","良")
