import requests
from bs4 import BeautifulSoup
import time
import constant as con
import component.result_list as rl
import component.race_info as ri
import component.pay_list as pl


def scrayping():
    # 変数宣言
    year = "2023"  # 任意の年数
    race_id = ""  # 宣言
    error = "true"  # エラーフラグ
    result_list = []  # result_rankingの子要素
    race_list = []  # race_infoの子要素
    pay_list = []  # result_payの子要素
    one_race = []    # 1レースごと
    one_day = []     # 一日のレース情報
    one_open_day = []  # 開催ごと

    # 指定した年から現在の日付までの全レースを取得
    for where in con.race_venue_list:
        error = "true"
        for howtime in range(con.HOWTIME):
            for howdays in range(con.HOWDAYS):
                for race_num in range(con.RACENUM):

                    # 文字に変換
                    ht = str(howtime+1)
                    hd = str(howdays+1)
                    rn = str(race_num+1)

                    # 1桁の数字を0付きの2桁の数字に変換
                    race_id = year + where + ('0' + ht if len(ht) != 2 else ht) + (
                        '0' + hd if len(hd) != 2 else hd) + ('0' + rn if len(rn) != 2 else rn)

                    # URLを作成
                    # url = f"https://race.netkeiba.com/race/result.html?race_id={race_id}&rf=race_list"
                    url = f"https://db.netkeiba.com/race/{race_id}"

                    # スクレイピング
                    res = requests.get(url)
                    res.encoding = "EUC-JP"
                    soup = BeautifulSoup(res.text, "html.parser")


                    # 正常にスクレイピングが行われた場合（ここの条件式は変更の余地あり）
                    if "レース結果" in soup.text:

                        # 上位５馬の馬枠、馬番、馬名を取得しresult_listに格納
                        result_list = rl.get_only(soup, race_id)
                        # レース情報を取得しrace_listに格納
                        race_list = ri.get(soup, race_id)
                        # 払い戻し情報を取得しpay_listに格納
                        pay_list = pl.get(soup, race_id)

                        one_race.append(result_list)
                        one_race.append(race_list)
                        one_race.append(pay_list)
                        print(race_id)

                        # 1秒待つ
                        time.sleep(1)

                    else:
                        error = "false"
                        break
                    # -----race_num-----
                    one_day.append(one_race)
                    one_race = []    # one_raceを初期化
                if error == "false":
                    break
                # -----howdays-----

                one_open_day.append(one_day)

                one_day = []      # one_dayを初期化

            if error == "false":
                break

            # １開催分
            # return(one_open_day)
            # -----howtime-----

        # 1開催場分
        # return(total_info)

    # return(total_info)
