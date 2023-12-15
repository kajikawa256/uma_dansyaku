import requests
from bs4 import BeautifulSoup
import classes.db_operation_class as db
import re
import data.constant as con
import component.count_horse_num as count
from datetime import datetime
import pandas as pd
import sqlalchemy as sa
import datetime as now
from datetime import datetime
import datetime
import component.day_check  as check
import classes.db_operation_class as db

db_instans = db.Main()


# race_id = "202304040507"    # 直線のレース
# race_id = "201005030212" # １位が同着のレース（例外）
# race_id = "202304010410"
# race_id = "202301010101" #通常のレース
# race_id = "202309040401" #障害レース
# race_id = "202304040507" #小雨のレース
# race_id = "202306010101"
# race_id = "202305040901"
        #   "202305011001"
        #   "202305050301"
race_id = "202301010102"

# レースの発走時間を取得
starting_time = db_instans.get_starting_time(race_id)
dt1 = datetime.datetime.strptime(starting_time, '%H:%M')
dt2 = dt1 + datetime.timedelta(minutes=-30) # 発走時間の30分前

# crontabで監視
schedule = 'm h * * *'
schedule = schedule.replace("m",str(dt2.minute))
schedule = schedule.replace("h",str(dt2.hour))

print(schedule)




# times = time.split(":")




# url = f"https://race.netkeiba.com/race/shutuba.html?race_id={race_id}"
# # url = f"https://race.netkeiba.com/race/result.html?race_id={race_id}&rf=race_list"
# # url = f"https://db.netkeiba.com/race/{race_id}"
# res = requests.get(url)
# res.encoding = "EUC-JP"
# soup = BeautifulSoup(res.text, "html.parser")



# copy_instans.insert(soup,race_id)



# engine = sa.create_engine(
#     sa.engine.url.URL.create(
#         drivername="mysql", # or postgresql
#         username="root",  # e.g. "my-database-user"
#         password="root",  # e.g. "my-database-password"
#         host="127.0.0.1",  # e.g. "127.0.0.1"
#         port=3306,  # e.g. 3306
#         database="umadan",  # e.g. "my-database-name"
#     )
# )

# sql_query="""
# select * from result_horse where race_id = 202301010101;
# """
# df = pd.read_sql(sql=sql_query, con=engine)

# df = df.drop('RANKING', axis=1)

# print(df)
    


#     # dbにinsertする
#     # self.db.insert(con.TABLE[con.RESULT_HORSE],result_list)
# # pre.create_df(race_id)
# # tableInstans = table.Main()
# # tableInstans.insert(soup,race_id)


# # RaceData01 = soup.find(class_="RaceData01").text.replace("\n","").replace(" ","").split("/")
# # RaceData = re.split("[/]",RaceData01)

# # print(RaceData01)

# # dbInstans = db.Main()
# # dbInstans.get_race_id()

# # pl.insert_hit_detail(soup, race_id)
# # from sqlalchemy import create_engine
# # from sqlalchemy.orm import sessionmaker
# # import sqlalchemy as sa
# # import pandas as pd

# # engine = sa.create_engine(
# #     sa.engine.url.URL.create(
# #         drivername="mysql", # or postgresql
# #         username="umadan",  # e.g. "my-database-user"
# #         password="umadan",  # e.g. "my-database-password"
# #         host="localhost",  # e.g. "127.0.0.1"
# #         port="3306",  # e.g. 3306
# #         database="umadan",  # e.g. "my-database-name"
# #     )
# # )

# # engine = create_engine("mysql://umadan:umadan@localhost/umadan")



# # sql_query="select * from RACE;"
# # df = pd.read_sql(sql=sql_query, con=engine)

# # print(df)

