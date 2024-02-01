import sqlalchemy as sa
import data.constant as con
import pandas as pd

#ウマ
class HORSE():
    # コンストラクタ
    def __init__(self):
        self.engine = sa.create_engine(
        sa.engine.url.URL.create(
            drivername = "mysql",       # or postgresql
            username = con.USER,        # e.g. "my-database-user"
            password = con.PASSWORD,    # e.g. "my-database-password"
            host = con.HOST,            # e.g. "127.0.0.1"
            port = con.PORT,            # e.g. 3306
            database = con.DB,          # e.g. "my-database-name"
        )
        )

    #3着以内
    def wins(self,horse_id,racedate):
        if horse_id and racedate:
            sql_wins = f"select HNAME, count(RANKING) from RESULT_HORSE left join RACE on RESULT_HORSE.RACE_ID = RACE.RACE_ID where HNAME = '{horse_id}' and RACEDATE < '{racedate}' and RANKING <= 3 group by HNAME;"
            df = pd.read_sql(sql=sql_wins, con=self.engine)
        return df
    
    #レース総数
    def all_race(self,horse_id,racedate):
        if horse_id and racedate:
            sql_wins = f"select HNAME, count(RANKING) from RESULT_HORSE left join RACE on RESULT_HORSE.RACE_ID = RACE.RACE_ID where HNAME = '{horse_id}' and RACEDATE < '{racedate}' group by HNAME;"
            df = pd.read_sql(sql=sql_wins, con=self.engine)
        return df
    
    #3着以内率
    def win_lose(self,horse_id,racedate):
        win = int(self.wins(horse_id,racedate)['count(RANKING)'])
        race = int(self.all_race(horse_id,racedate)['count(RANKING)'])
        return float(win/race)
    
#騎手
class JOCKEY():
    # コンストラクタ
    def __init__(self):
        self.engine = sa.create_engine(
        sa.engine.url.URL.create(
            drivername = "mysql",       # or postgresql
            username = con.USER,        # e.g. "my-database-user"
            password = con.PASSWORD,    # e.g. "my-database-password"
            host = con.HOST,            # e.g. "127.0.0.1"
            port = con.PORT,            # e.g. 3306
            database = con.DB,          # e.g. "my-database-name"
        )
        )

    #3着以内
    def wins(self,jockey_id,racedate):
        if jockey_id and racedate:
            sql_wins = f"select count(RANKING) from RESULT_HORSE left join RACE on RESULT_HORSE.RACE_ID = RACE.RACE_ID where JOCKEY_ID = '{jockey_id}' and RACEDATE < '{racedate}' and RANKING <= 3;"
            df = pd.read_sql(sql=sql_wins, con=self.engine)
        return df
    
    #レース総数
    def all_race(self,jockey_id,racedate):
        if jockey_id and racedate:
            sql_wins = f"select count(RANKING) from RESULT_HORSE left join RACE on RESULT_HORSE.RACE_ID = RACE.RACE_ID where JOCKEY_ID = '{jockey_id}' and RACEDATE < '{racedate}';"
            df = pd.read_sql(sql=sql_wins, con=self.engine)
        return df
    
    #3着以内率
    def win_lose(self,jockey_id,racedate):
        win = int(self.wins(jockey_id,racedate)['count(RANKING)'])
        race = int(self.all_race(jockey_id,racedate)['count(RANKING)'])
        return float(win/race)