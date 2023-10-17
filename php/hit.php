<?php
    //connect.phpを読み込む
    include("connect.php");
try {
    // テスト用データ
    $table = "HIT";
    $columns = ['RACEDATE','COLLECT_PR','HIT_PR'];
    $values =[date('Y-m-d'), 34,56];

    // insert() 関数を呼び出す
    $result = INSERT($table, $columns, $values);
}catch(PDOException $p){
    echo "接続エラーが発生" . $p;
}


?>