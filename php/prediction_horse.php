<?php
    include("db.php");
try {
    $db = db_connect();
    echo "接続成功";
}catch(PDOException $p){
    echo "接続エラーが発生" . $p;
}
?>