import data.constant as con
import mysql.connector as db

def insert(table, datalist:list) :
    #変数宣言
    insertData = ""
    count = 0
    sql = ""

    try:
        # データベース接続情報
        dbconnect = db.connect(           
            user = con.USER,
            passwd = con.PASSWORD,
            host = con.HOST,
            db = con.DB
        )

        # カーソルを取得する
        cur = dbconnect.cursor()     

        # dataがintならそのまま、strならダブルクォーテーションを付けて一つの文字列とする
        for data in datalist:
            count += 1

            # dataがint型ならstr型に変換、str型ならダブルクォーテーションを付与。末尾にはカンマを結合
            insertData += str(data) if type(data) is int else f"'{data}'"
            insertData += ","
            
            if (table == "RESULT_HORSE" and count % 5 == 0) or ( table == "RACE" and count % 12 == 0 ) or (table == "HIT_DETAIL" and count % 6 == 0) :
                # 末尾のカンマを削除
                insertData = insertData[:-1]

                # SQL（データベースを操作するコマンド）を実行する
                sql = f"INSERT INTO {table} VALUES ({insertData});"
                cur.execute(sql)

                # SQLを実行したらinsertDataを初期化
                insertData = ""


        #コミット
        dbconnect.commit()

    except Exception as e:
        #エラーが発生したらロールバック
        dbconnect.rollback()
        print(f"Error Occurred: {e}")
        print()
        print(sql)

    finally:
        cur.close
        dbconnect.close()