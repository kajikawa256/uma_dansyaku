<?php
// db.phpを読み込む
 include("db.php");

//与えられた値をinsert文に挿入する
function INSERT($table,$column,$value){
    // カラム名と値の数が一致しなければfalseを返す
    if(count($column) !== count($value)){
        return false;
    }
    try{
        //DB接続
        $db = db_connect();

        //文字列結合
        $columns = implode(',',$column);
        $values = ':' . implode(', :', $column);
        // $values = ':' . implode(', :', $value);
        //空白削除
        $columns = str_replace(' ', '', $columns);
        $columns = str_replace('　', '', $columns);
        $values = str_replace(' ', '', $values);
        $values = str_replace('　', '', $values);
        
        //sql作成
        $sql =  "INSERT INTO $table($columns)VALUES($values)";
        // ステートメントの準備
        $stmt = $db -> prepare($sql);

        //$value[$i]: これはプレースホルダーにバインドされる値
        echo "テーブル名：" . $table;
        for ($i = 0; $i < count($column); $i++) {
            $stmt->bindParam(':' . $column[$i], $value[$i]);
            echo "\n" . $column[$i] . "：" . $value[$i]; 
        }
        echo $sql;

        //実行
        $stmt->execute();
    }catch(PDOException $p){
        $db->rollBack();
        exit("DBエラー".$p->getMessage());
    }
}   
?>