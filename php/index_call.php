<?php
include('def.php');
include('db.php');
$sql_race="";

if (isset($_GET['selectedValue'])) {
    $selectedValue = $_GET['selectedValue'];
}else{
    $selectedValue = '';
}
echo $selectedValue;


# 場所
if(array_key_exists('raceplace',$_GET)){
    $x = $_GET['raceplace'];
    $race_filter = "'$x'";
}else{
    $_GET['raceplace'] ="";
    $race_filter = ""; 
}

# 日付
if(array_key_exists('racedate',$_GET)){
    $y = $_GET['racedate'];
    $racedate_filter = "'$y'";
}else{
    $_GET['racedate'] ="";
    $racedate_filter = ""; 
}


/*
    Raceテーブルからindex.phpに表示するデータをreturnする関数 
*/


try{
    $db = db_connect();

    if(strcmp($race_filter,"") == 0 && strcmp($racedate_filter,"") == 0){
        $sql= "SELECT DISTINCT RACEDATE,PLACE
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
        $stmt = null;
        $racedate_filter = "'" . $race_filter_result[0]["RACEDATE"] . "'";
        $race_filter = "'" . $race_filter_result[0]["PLACE"] . "'";
    }

    
    $sql_race = "SELECT RACE_ID,
                    RACEDATE,
                    RNAME,
                    RACENUMBER,
                    TIME,
                    DISTANCE,
                    GROUND,
                    PLACE,
                    WEATHER 
                FROM $table[$RACE]
                WHERE RACEDATE = $racedate_filter
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

    if(empty($result_race)){
        $sql_race = "SELECT RACE_ID,
                    RACEDATE,
                    RNAME,
                    RACENUMBER,
                    TIME,
                    DISTANCE,
                    GROUND,
                    PLACE,
                    WEATHER 
                FROM $table[$RACE]
                WHERE RACEDATE = $racedate_filter"; 
  

        //ステートメントの準備
        $stmt = $db->prepare($sql_race);
        
        //実行
        $stmt->execute();
        $result_race = [];
        while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
            $result_race[] = $rows;
        }
        $stmt = null;
    }
  
    
/*
    Raceテーブルからindex.phpに日付をプルダウン形式で表示するデータ 
*/


$sql_date = "SELECT DISTINCT RACEDATE FROM RACE ORDER BY RACEDATE DESC;";
//ステートメントの準備
$stmt = $db->prepare($sql_date);
//実行
$stmt->execute();
$result_race_date = [];
while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
    $result_race_date[] = $rows;
}
$stmt = null;


/*
    Raceテーブルからindex.phpに場所をプルダウン形式で表示するデータ 
*/


$sql_date = "SELECT DISTINCT PLACE FROM RACE ORDER BY RACEDATE DESC;";
//ステートメントの準備
$stmt = $db->prepare($sql_date);
//実行
$stmt->execute();
$result_race_place = [];
while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
    $result_race_place[] = $rows;
}
$stmt = null;


/*
    Raceテーブルからindex.phpの開催場を表示するデータをreturnする関数 
*/


if(strcmp($racedate_filter,"") == 0){
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
}else{
    $sql_race_place = "SELECT DISTINCT PLACE 
    FROM $table[$RACE] 
    WHERE RACEDATE = $racedate_filter";

    //ステートメントの準備
    $stmt = $db->prepare($sql_race_place);
    //実行
    $stmt->execute();
    $result_race_place = [];
    while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
    $result_race_place[] = $rows;
    }
    $stmt = null;
}


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
    $stmt = null;


/*
    一日に開催されたレースをカウントする関数
*/


if(strcmp($race_filter,"") == 0 && strcmp($racedate_filter,"") == 0){
    $sql_racecount_first = "SELECT RACEDATE, COUNT(*) as racecount
                        FROM $table[$RACE]
                        WHERE RACEDATE = (
                            SELECT MAX(RACEDATE) FROM RACE
                        );";
    $stmt = $db->prepare($sql_racecount_first);
    //実行
    $stmt->execute();
    $result_racecount = [];
    while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
        $result_racecount[]= $rows;
    }
    $stmt = null;
    $racedate_filter = "'" . $result_racecount[0]["RACEDATE"] . "'";
}

$sql_racecount = "SELECT RACEDATE, COUNT(*) as racecount
                FROM $table[$RACE]
                WHERE RACEDATE = $racedate_filter;"; 
  

    //ステートメントの準備
    $stmt = $db->prepare($sql_racecount);
    
    //実行
    $stmt->execute();
    $result_racecount = [];
    while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
        $result_racecount[] = $rows;
    }
    $stmt = null;


/*
    日付ごとの単勝的中数を数える関数
*/


if(strcmp($race_filter,"") == 0 && strcmp($racedate_filter,"") == 0){
    $sql_hitcount_first = "SELECT RACE.RACEDATE, COUNT(*) as hitcount
                        FROM $table[$RACE] RACE
                        JOIN RESULT_HORSE RESULT
                        ON RACE.RACE_ID = RESULT.RACE_ID
                        JOIN PREDICTION_HORSE PREDICTION
                        ON RACE.RACE_ID = PREDICTION.RACE_ID
                        WHERE RACEDATE = (
                            SELECT MAX(RACEDATE) FROM $table[$RACE]
                        )
                        AND RESULT.HORSENUMBER = PREDICTION.HORSENUMBER
                        AND RESULT.RANKING = '1' AND PREDICTION.RANKING = '1';";
    $stmt = $db->prepare($sql_hitcount_first);
    //実行
    $stmt->execute();
    $result_hitcount_first = [];
    while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
        $result_hitcount_first[]= $rows;
    }
    $stmt = null;
    $racedate_filter = "'" . $result_hitcount_first[0]["RACEDATE"] . "'";
}
$sql_hitcount = "SELECT RACE.RACEDATE, COUNT(*) as hitcount
                        FROM $table[$RACE] RACE
                        JOIN RESULT_HORSE RESULT
                        ON RACE.RACE_ID = RESULT.RACE_ID
                        JOIN PREDICTION_HORSE PREDICTION
                        ON RACE.RACE_ID = PREDICTION.RACE_ID
                        WHERE RACEDATE = $racedate_filter
                        AND RESULT.HORSENUMBER = PREDICTION.HORSENUMBER
                        AND RESULT.RANKING = '1' AND PREDICTION.RANKING = '1';";
$stmt = $db->prepare($sql_hitcount);
//実行
$stmt->execute();
$result_hitcount = [];
while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
    $result_hitcount[]= $rows;
}
$stmt = null;

/*
    回収率を求める関数 式
*/

if(strcmp($race_filter,"") == 0 && strcmp($racedate_filter,"") == 0){
    $sql_collect_first = "SELECT SUM(HIT.BETBACK) as collect
                        FROM $table[$RACE] RACE
                        JOIN RESULT_HORSE RESULT
                        ON RACE.RACE_ID = RESULT.RACE_ID
                        JOIN PREDICTION_HORSE PREDICTION
                        ON RACE.RACE_ID = PREDICTION.RACE_ID
                        JOIN HIT_DETAIL HIT
                        ON RACE.RACE_ID = HIT.  RACE_ID
                        WHERE RACEDATE = (
                            SELECT MAX(RACEDATE) FROM $table[$RACE]
                        )
                        AND RESULT.HORSENUMBER = PREDICTION.HORSENUMBER
                        AND PREDICTION.HORSENUMBER = HIT.HORSENUMBER 
                        AND HIT.HORSENUMBER NOT LIKE '%-%' AND HIT.HORSENUMBER NOT LIKE '%>%'
                        AND RESULT.RANKING = '1' AND PREDICTION.RANKING = '1'
                        AND HIT.KINDS = '単勝';";
    $stmt = $db->prepare($sql_collect_first);
    //実行
    $stmt->execute();
    $result_collect_first = [];
    while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
        $result_collect_first[]= $rows;
    }
    $stmt = null;
    $racedate_filter = "'" . $result_collect_first[0]["RACEDATE"] . "'";
}
$sql_collect = "SELECT SUM(HIT.BETBACK) as collect
                        FROM $table[$RACE] RACE
                        JOIN RESULT_HORSE RESULT
                        ON RACE.RACE_ID = RESULT.RACE_ID
                        JOIN PREDICTION_HORSE PREDICTION
                        ON RACE.RACE_ID = PREDICTION.RACE_ID
                        JOIN HIT_DETAIL HIT
                        ON RACE.RACE_ID = HIT.RACE_ID
                        WHERE RACEDATE = $racedate_filter
                        AND RESULT.HORSENUMBER = PREDICTION.HORSENUMBER
                        AND PREDICTION.HORSENUMBER = HIT.HORSENUMBER
                        AND HIT.HORSENUMBER NOT LIKE '%-%' AND HIT.HORSENUMBER NOT LIKE '%>%'
                        AND RESULT.RANKING = '1' AND PREDICTION.RANKING = '1'
                        AND HIT.KINDS = '単勝';";
    $stmt = $db->prepare($sql_collect);
    //実行
    $stmt->execute();
    $result_collect = [];
    while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
        $result_collect[]= $rows;
    }   
}catch(PDOException $poe) {
    echo $sql_race;
    $db->rollBack();
    var_dump ($race_filter);
    exit("DBエラー".$poe->getMessage());
    
}finally{
    $stmt = null;
    $db = null;
}
?>