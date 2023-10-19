<?php
    //connect.phpを読み込む
    include("connect.php");
try {
    // テスト用データ
    $table = "RACE";
    $columns = ['RACE_ID', 'RACEDATE','RNAME','RACENUMBER','TIME','DISTANCE','HORSE_TOTAL','GROUND','PLACE','WEATHER','SPIN','SITUATION'];
    $values = [
        ['2003090901', '2001-10-17', '2歳未勝利', '10', '10:17:01', 1800, 14, '芝', '札幌', '晴', '右', '重'],
        ['2003090902', '2002-10-17', '3歳未勝利','11', '10:17:02', 2400, 15, 'ダート', '阪神', '雨', '左', '稍重']
    ];

    // insert() 関数を呼び出す
    $result = INSERT($table, $columns, $values);
    
       
}catch(PDOException $p){
    echo "接続エラーが発生" . $p;
}
?>