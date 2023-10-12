<?php
// db.phpを読み込む
include("db.php");

//与えられた値をinsert文に挿入する
function insert($table,$x,$y){
    // カラム名と値の数が一致しなければfalseを返す
    if(count($x) !== count($y)){
        return false;
    }

    //db接続
    $db = db_connect();

    //insert文の生成
    $columns = implode(',',$x);
    $values = ':' . implode(',',$y);
    $sql = "INSERT INTO $table ($columns) VALUES ($values)";

     // ステートメントの準備
     $stmt = $db->prepare($sql);

     // カラム名と値のバインド（関連付ける）
    foreach ($x as $column) {
        $stmt->bindParam(":$column", $y[array_search($column, $x)]);
    }
}
?>