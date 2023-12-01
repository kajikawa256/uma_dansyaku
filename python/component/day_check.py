import datetime as now
from datetime import datetime

# スクレイピング対象のページの日付が明日の日付かならTrue,それ以外ならfalseを返す関数
def day_next(date):
    # 明日の日付を生成
    dt_now = now.datetime.now() 
    dt2 = dt_now + now.timedelta(days=1)
    tomorrow_date =dt2.strftime("%Y年%m月%d日")

    if date == tomorrow_date:
        return True
    else:
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

# 現在の日付取得
def get_now_date():
    dt_now = now.datetime.now() 
    now_date = dt_now.strftime("%Y年%m月%d日")

    now_date = now_date[:4]
    return int(now_date)
   


