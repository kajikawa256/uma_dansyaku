<?php
    //connect.phpを読み込む
    include("connect.php");
try {
    // テスト用データ
    $table = "HIT_DETAIL";
    $columns = ['RACE_ID','KINDS','HORSENUMBER','HORSEFRAME','BETBACK','POPULAR'];
    $values =['2003090901','単勝',1,12,5400,10];

    // insert() 関数を呼び出す
    $result = INSERT($table, $columns, $values);
}catch(PDOException $p){
    echo "接続エラーが発生" . $p;
}


?>