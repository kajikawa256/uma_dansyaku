<?php
include('def.php');
include('db.php');

if(array_key_exists('place',$_GET)){
    $x = $_GET['place'];
    $race_filter = "'$x'";
}else{
    $race_filter = ""; 
}

/*
    Raceテーブルからindex.phpに表示するデータをreturnする関数 
*/
try{
    $db = db_connect();

    if(strcmp($race_filter,"") == 0){
        $sql= "SELECT DISTINCT PLACE
        FROM $table[$RACE]
        WHERE RACEDATE = (
            SELECT MAX(RACEDATE) FROM RACE
        )
        limit 1;"; 

        $stmt = $db->prepare($sql);
        //実行
        $stmt->execute();
        $race_filter_result = [];
        while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
            $race_filter_result[]= $rows;
        }
        var_dump($race_filter_result);
        $stmt = null;
        $race_filter = "'" . $race_filter_result[0]["PLACE"] . "'";
    }
    $sql_race = "SELECT RACE_ID,RACEDATE,
                        RNAME,
                        RACENUMBER,
                        TIME,
                        DISTANCE,
                        GROUND,
                        PLACE,
                        WEATHER 
    FROM $table[$RACE]
    WHERE RACEDATE = (
        SELECT MAX(RACEDATE) FROM RACE
    )
    AND PLACE = $race_filter;"; 



         
    //ステートメントの準備
    $stmt = $db->prepare($sql_race);
    //実行
    $stmt->execute();
    $result_race = [];
    while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
        $result_race[] = $rows;
    }
    $stmt = null;
/*
    Raceテーブルからindex.phpの開催場を表示するデータをreturnする関数 
*/
    $sql_race_place = "SELECT DISTINCT PLACE 
                        FROM $table[$RACE] 
                        WHERE RACEDATE = (
                            SELECT MAX(RACEDATE) FROM RACE
                        );";
  
    //ステートメントの準備
    $stmt = $db->prepare($sql_race_place);
    //実行
    $stmt->execute();
    $result_race_place = [];
    while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
        $result_race_place[] = $rows;
    }
    $stmt = null;

    /*
        Hitテーブルからindex.phpに表示するデータをreturnする関数 
    */
    $sql_hit = "SELECT * FROM $table[$HIT]";
    //ステートメントの準備
    $stmt = $db->prepare($sql_hit);
    //実行
    $stmt->execute();
    $result_hit = [];
    while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
        $result_hit[] = $rows;
    }

}catch(PDOException $poe) {
    echo $sql_race;
    $db->rollBack();
    exit("DBエラー".$poe->getMessage());
    
}finally{
    $stmt = null;
    $db = null;
}
?>