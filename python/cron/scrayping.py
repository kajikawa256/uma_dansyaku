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
# cron_instans = cron.CrontabControl()          # cronの実行タイミングを設定するインスタンス
exclusionIDList = []                            # 除外race_idリスト
raceIdList = cr.get_id()                        # race_idのリストを生成


# race_idのリストを基にスクレイピングを行う
for race_id in tqdm(raceIdList):
    # URLを作成
    url = f"https://race.netkeiba.com/race/result.html?race_id={race_id}"

    # 除外リストに含まれていたらスキップ
    if race_id in exclusionIDList:
        continue

    # URLを基にスクレイピング
    soup = sc.get_soup(url)
    time.sleep(1)

    # 存在するページならbs4で解析
    if "出馬表" in soup.text:
        # 日付の確認
        now_date, date = get_day.day_check(race_id,soup)
        flag = get_day.day_next(date)
        # flag2 = get_day.day_check2()
        # flag3 = get_day.confilm_check(date)

        # 日付が現在より未来以上　かつ　スクレイピング対象ページの日付が今日を含め1日後なら
        if now_date <= date and flag:
            # flag2 = False if now_date == date else flag2
            # # 対象の日付が今日と同じだが、23じ以降ならセレニウムはskip(レース結果が確定しているため)
            # if flag3:
            #     print("aaa")
            #     continue
            # # 今日の日付が次の日の1日前の12時以前ならskip
            # if flag2:
            #     print("aaa")
            #     continue
            # レースの日付が今日以降の場合出馬表のスクレイピング実行
            url = f"https://race.netkeiba.com/race/shutuba.html?race_id={race_id}&rf=race_submenu"
            # スクレイピング
            soup = sc.get_soup(url)
            # 各テーブルに対応したデータをinsertする
            insert_instans.insert_plan(soup,race_id)
            predict_instans.predict(race_id)
            continue

        # 日付が現在より未来で　かつ　スクレイピング対象ページの日付が1日後じゃなければ
        if now_date < date and flag != True:
            continue

        # 各テーブルに対応したデータをinsertする
        insert_instans.insert(soup,race_id)
        predict_instans.predict(race_id)
    else:
        #race_idの末尾が01でなければaddEscapeListを呼び出さない
        if race_id[-2:] != "01":
            continue

        # 除外race_idのリストを生成する
        exclusionIDList = (escape.addEscapeList(race_id, list(exclusionIDList)))
        exclusionIDList = set(exclusionIDList)
