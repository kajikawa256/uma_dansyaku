<?php
    //connect.phpを読み込む
    include("connect.php");
try {
    // テスト用データ
    $table = "PREDICTION_HORSE";
    $columns = ['RACE_ID','HNAME','HORSENUMBER','HORSEFRAME','RANKING'];
    $values =[
        ['2003090901','ディープインパクト',1,2,1],
        ['2003090901','キタサンブラック',2,2,2]
    ];

    // insert() 関数を呼び出す
    $result = INSERT($table, $columns, $values);
}catch(PDOException $p){
    echo "接続エラーが発生" . $p;
}


?>