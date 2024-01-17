from tqdm import tqdm
import time
import component.create_raceID as cr
import component.scrayping_def as sc
import classes.create_scrayping_list as create_list
import classes.db_operation_class as db
import data.constant as con


raceIdList = cr.update_race_id()        # 結果確定していないレースIDの取得
insert_instans = create_list.Main()     # インスタンスの作成
db_instans = db.Main()                  # インスタンスの作成

for race_id in tqdm(raceIdList):
    # URLを作成
    url = f"https://race.netkeiba.com/race/result.html?race_id={race_id}"

    # URLを基にスクレイピング
    soup = sc.get_soup(url)
    time.sleep(1)

    if "馬番" in soup.text:
        # 古いデータを削除
        db_instans.delete(con.TABLE[con.RACE],race_id)
        db_instans.delete(con.TABLE[con.RESULT_HORSE],race_id)

        # 各テーブルに対応したデータをinsertする
        insert_instans.insert(soup,race_id)