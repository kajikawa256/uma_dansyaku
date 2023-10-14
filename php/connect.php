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
        $values = implode(',',$value);
        // $values = ':' . implode(', :', $value);
        //空白削除
        $columns = str_replace(' ', '', $columns);
        $columns = str_replace('　', '', $columns);
        $values = str_replace(' ', '', $values);
        $values = str_replace('　', '', $values);
        
        // //テスト
        // var_dump($columns);
        // var_dump($values);

        //sql作成
        $sql =  "INSERT INTO $table($columns)VALUES($values)";
        echo $sql;
        // ステートメントの準備
        $stmt = $db -> prepare($sql);
        // echo "実行成功";

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
$table = "RACE";
$columns = ['RACE_ID', 'RACEDATE','RNAME','RACENUMBER','TIME','DISTANCE','HORSE_TOTAL','GROUND','PLACE','WEATHER','SPIN','SITUATION'];
$values = ['2003090901', date('Y-m-d'), '2歳未勝利','10R',date('H:i:s'),1800,14,'芝','札幌','晴れ','右','重'];

// insert() 関数を呼び出す
$result = insert($table, $columns, $values);
    
?>