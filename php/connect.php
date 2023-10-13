<?php
// db.phpを読み込む
 include("db.php");

//与えられた値をinsert文に挿入する
function insert($table,$column,$value){
    // カラム名と値の数が一致しなければfalseを返す
    if(count($column) !== count($value)){
        return false;
    }

    try{
        //DB接続
        $db = db_connect();

        //文字列結合
        $columns = implode(',',$column);
        $values = ':' . implode(', :', $value);
        //空白削除
        $columns = str_replace(' ', '', $columns);
        $columns = str_replace('　', '', $columns);
        $values = str_replace(' ', '', $values);
        $values = str_replace('　', '', $values);
        
        //テスト
        var_dump($columns);
        var_dump($values);

        //sql作成
        $sql =  "INSERT INTO $table($columns)VALUES($values)";

        // ステートメントの準備
        $stmt = $db -> prepare($sql);
        echo "実行成功";

        // カラム名と値のバインド（関連付ける）
        $stmt->bindParam(1, $table);
        $stmt->bindParam(2, $columns);
        $stmt->bindParam(3, $values);

        //実行
        $stmt->execute();
    }catch(PDOException $p){
        $db->rollBack();
        exit("DBエラー".$p->getMessage());
    }
    $db->rollBack();
}

// テスト用データ
$table = "example_table";
$columns = ['column1', 'column2', 'column3'];
$values = ['value1', 'value2', 'value3'];

// insert() 関数を呼び出す
$result = insert($table, $columns, $values);
    
?>