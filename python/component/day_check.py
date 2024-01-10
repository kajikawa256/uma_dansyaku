import datetime as now
from datetime import datetime, timedelta

# スクレイピング対象のページの日付が明日の日付ならTrue,それ以外ならfalseを返す関数
def day_next(date):
    # 明日の日付を生成
    dt_now = now.datetime.now()
    dt2 = dt_now + now.timedelta(days=1)
    tomorrow_date =dt2.strftime("%Y年%m月%d日")
    dt_now = dt_now.strftime("%Y年%m月%d日")

    # １日後、または同じ日付ならTrue
    if date == tomorrow_date or date == dt_now:
        return True
    else:
        return False


def confilm_check(date):

    # 今日の日付と時刻を取得
    now = datetime.now()

    # 対象の日付が今日と同じ日付かつ23時以降かどうかを判定
    if now.hour >= 23 and date == datetime.strptime(now,"%Y年%m月%d日"):
        # print("対象の日付が今日と同じ日付かつ23時以降です。")
        return True
    else:
        # print("対象の日付が今日と同じ日付ですが23時以前です")
        return False


# 本日の日付とスクレイピング対象のページの日付の取得
def day_check(race_id,soup):
  # 現在の日付
  dt_now = now.datetime.now()
  now_date = dt_now.strftime("%Y年%m月%d日")

  # スクレイピングしたページの日付
  year = race_id[0:4] + "年"
  month_day = soup.find("dd",class_="Active").text
  if "/" in month_day:
      month_day = month_day.replace("/","月")
      month_day += "日"
  else:
      month_day = month_day[:-3]
  date_object = datetime.strptime(year + month_day, "%Y年%m月%d日")
  date = date_object.strftime("%Y年%m月%d日")

  return now_date, date


# 今日の日付が次の日の1日前の12時以降かどうかチェック
def day_check2():
    # 現在の日付と時刻を取得
    now = datetime.now()
    # 次の日の日付を計算
    next_day = now + timedelta(days=1)
    # 次の日の1日前の12時の時刻を計算
    target_time = datetime(next_day.year, next_day.month, next_day.day, 12, 0, 0)

    # 判定
    if now >= target_time:
        # print("今日の日付が次の日の1日前の12時以降です。")
        return False
    else:
        # print("今日の日付が次の日の1日前の12時以前です。")
        return True


# 現在の日付取得
def get_now_date():
    dt_now = now.datetime.now() 
    now_date = dt_now.strftime("%Y年%m月%d日")

    now_date = now_date[:4]
    return int(now_date)



