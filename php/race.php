<?php
    //connect.phpを読み込む
    include("connect.php");
try {
    // テスト用データ
    $table = "RACE";
    $columns = ['RACE_ID', 'RACEDATE','RNAME','RACENUMBER','TIME','DISTANCE','HORSE_TOTAL','GROUND','PLACE','WEATHER','SPIN','SITUATION'];
    $values = ['2003090901', date('Y-m-d'), '2歳未勝利','10',date('H:i:s'),1800,14,'芝','札幌','晴れ','右','重'];

    // insert() 関数を呼び出す
    $result = INSERT($table, $columns, $values);
}catch(PDOException $p){
    echo "接続エラーが発生" . $p;
}


?>