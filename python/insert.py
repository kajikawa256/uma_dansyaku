# 他ファイル呼び出し
import constant as cons

# module呼び出し
import mysql.connector as db

def insert(table, datalist:list) :

    # SQL文に入れれるようにデータの形を整形
    SQLdatalist
    for data in datalist :
        if type(data) is str :
            SQLdatalist = data

    try:
        # データベース接続情報
        dbconnect = db.connect(           
            user = "hoge",
            passwd = "hogehoge",
            host = "hoge",
            db = "mysql"
        )

        # カーソルを取得する
        cur = dbconnect.cursor()           

        # SQL（データベースを操作するコマンド）を実行する
        sql = f"INSERT INTO {cons.TABLE[table]} VALUES('')"
        cur.execute(sql)

        dbconnect.commit()
        


    except Exception as e:
        print(f"Error Occurred: {e}")

    finally:
        cur.close
        dbconnect.close()