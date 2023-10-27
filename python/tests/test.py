import requests
from bs4 import BeautifulSoup
import component.race_list as rl

# race_id = "201005030211" # １位が同着のレース（例外）
# race_id = "202301020112"
# race_id = "202301010101" #通常のレース
race_id = "202309040401" #障害レース
url = f"https://db.netkeiba.com/race/{race_id}"
res = requests.get(url)
res.encoding = "EUC-JP"
soup = BeautifulSoup(res.text, "html.parser")

rl.insert_race(soup,race_id)





# pl.insert_hit_detail(soup, race_id)
# from sqlalchemy import create_engine
# from sqlalchemy.orm import sessionmaker
# import sqlalchemy as sa
# import pandas as pd

# engine = sa.create_engine(
#     sa.engine.url.URL.create(
#         drivername="mysql", # or postgresql
#         username="umadan",  # e.g. "my-database-user"
#         password="umadan",  # e.g. "my-database-password"
#         host="localhost",  # e.g. "127.0.0.1"
#         port="3306",  # e.g. 3306
#         database="umadan",  # e.g. "my-database-name"
#     )
# )

# engine = create_engine("mysql://umadan:umadan@localhost/umadan")



# sql_query="select * from RACE;"
# df = pd.read_sql(sql=sql_query, con=engine)

# print(df)

