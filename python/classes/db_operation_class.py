import data.constant as con
import mysql.connector as db

class Main:
    # コンストラクタ db接続を行いクラス変数に保存
    def __init__(self):
        try:
            # データベース接続情報
            self.dbconnect = db.connect(           
                user = con.USER,
                passwd = con.PASSWORD,
                host = con.HOST,
                db = con.DB
            )

            # オートコミットoff
            self.dbconnect.autocommit = False

            # カーソル情報
            self.dbconnect.is_connected()
            self.cur = self.dbconnect.cursor()

        except Exception as e:
            print(f"Error Occurred: {e}")


    # insert関数
    def insert(self,table, datalist:list) :
        #変数宣言
        insertData = ""
        count = 0
        sql = ""

        try:
            # dataがintならそのまま、strならダブルクォーテーションを付けて一つの文字列とする
            for data in datalist:
                count += 1

                # dataがint型ならstr型に変換、str型ならダブルクォーテーションを付与。末尾にはカンマを結合
                insertData += str(data) if type(data) is int else f"'{data}'"
                insertData += ","
                
                if (table == "RESULT_HORSE" and count % len(con.COLUMNS[con.RESULT_HORSE]) == 0) or ( table == "RACE" and count % len(con.COLUMNS[con.RACE]) == 0 ) or (table == "HIT_DETAIL" and count % len(con.COLUMNS[con.HIT_DETAIL]) == 0) :
                    # 末尾のカンマを削除
                    insertData = insertData[:-1]

                    # SQL（データベースを操作するコマンド）を実行する
                    sql = f"INSERT INTO {table} VALUES ({insertData});"
                    self.cur.execute(sql)

                    # SQLを実行したらinsertDataを初期化
                    insertData = ""

            #コミット
            self.dbconnect.commit()

        except Exception as e:
            #エラーが発生したらロールバック
            self.dbconnect.rollback()
            print(f"Error Occurred: {e}")
            print(sql)


    # RACEテーブルに保存されているrace_idを取得
    def get_race_id(self):
        try:
            sql = "SELECT RACE_ID FROM RACE;"
            self.cur.execute(sql)

            # 実行結果を使いやすいようにリスト化
            race_id = self.cur.fetchall()
            race_id_list = [item[0] for item in race_id]
            
            # 取得済みのrace_idをreturn
            return race_id_list

        except Exception as e:
            print(f"Error Occurred: {e}")


    # RACEテーブルにのみ存在するRACE_IDを取得
    def get_need_update_race_id(self):
        try:
            sql = "SELECT RACE_ID FROM RACE WHERE WEATHER = '-' ORDER BY TIME ASC LIMIT 1;"
            self.cur.execute(sql)

            # リストを変数に
            race_idList = self.cur.fetchall()

            if race_idList == []:
                return None

            race_id = race_idList[0]
            race_id = str(race_id).replace(",","").replace("(","").replace(")","").replace("'","")
            
            # 取得済みのrace_idをreturn
            return race_id

        except Exception as e:
            print(f"Error Occurred: {e}")

    
    # レース直前に天気と馬場状態をupdateする関数
    def race_update(self,race_id,weather,situation):
        if race_id == None:
            return
        try:
            # 天気の更新
            sql = f"UPDATE RACE SET WEATHER = '{weather}' WHERE RACE_ID = {race_id}"
            self.cur.execute(sql)
            # 馬場状態の更新
            sql = f"UPDATE RACE SET SITUATION = '{situation}' WHERE RACE_ID = {race_id}"
            self.cur.execute(sql)

            #コミット
            self.dbconnect.commit()

        except Exception as e:
            #エラーが発生したらロールバック
            self.dbconnect.rollback()
            print(sql)
            print(f"Error Occurred: {e}")

    
    # デストラクタ 処理が終わればcloseする
    def __del__(self):
        try:
            self.dbconnect.close()
        except Exception as e:
            print(e)