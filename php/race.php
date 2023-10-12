<?php
    //db.phpを読み込む
    include("db.php");
try {
    $db = db_connect();
    echo "接続成功";
}catch(PDOException $p){
    echo "接続エラーが発生" . $p;
}

//sql作成
$db->beginTransaction();

$X = "RACE_ID,RACEDATE,RNAME,RACENUMBER,TIME,DISTANCE,HORSE_TOTAL,GROUND,PLACE,WEATHER,SPIN,SITUATION";
$Y ="(
    
)";
$TABLE = 'RACE';

$sql = 'INSERT INTO $TABLE ($X) VALUES ($y)';

?>