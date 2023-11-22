<?php
include('def.php');
include('db.php');
$sql_race="";

if (isset($_GET['selectedValue'])) {
    $selectedValue = $_GET['selectedValue'];
}else{
    $selectedValue = '';
}


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

    if(strcmp($race_filter,"") == 0 || strcmp($racedate_filter,"") == 0){
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

        if(empty($result_race)){
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
        }

        # その場所のレースのみを取得する
        if(count($result_race) > 11){
            $race_filter = "'" .$result_race[0]["PLACE"]. "'";
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
        }
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


if(strcmp($racedate_filter,"") == 0){
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