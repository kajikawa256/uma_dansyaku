<?php
    //connect.phpを読み込む
    include("connect.php");
try {
    // テスト用データ
    $table = "HIT";
    $columns = ['RACEDATE','COLLECT_PR','HIT_PR'];
    $values =[
        ['2001-10-18', 7, 56],
        ['2002-10-19', 8, 57]
    ];

    // insert() 関数を呼び出す
    $result = INSERT($table, $columns, $values);
    // if ($stmt->rowCount() > 0) {
    //     return true;  // 成功した場合に true を返す
    // } else {
    //     return false; // 失敗した場合に false を返す
    // }
}catch(PDOException $p){
    echo "接続エラーが発生" . $p;
}   


?>