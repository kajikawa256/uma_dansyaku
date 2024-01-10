<?php 
//最新の日付と場所
function getMaxRaceQuery(){
    $sql= "SELECT DISTINCT RACEDATE,PLACE
    FROM RACE
    WHERE RACEDATE = (
        SELECT MAX(RACEDATE) FROM RACE
    )
    limit 1;"; 
    return $sql;
}

//日付に基づいたレース情報
function getOneFilterQuery($racedate_filter){
    $sql = "SELECT RACE_ID,
                    RACEDATE,
                    RNAME,
                    RACENUMBER,
                    TIME,
                    DISTANCE,
                    GROUND,
                    PLACE,
                    WEATHER 
                FROM RACE
                WHERE RACEDATE = $racedate_filter
                ORDER BY RACENUMBER ASC"; 
    return $sql;
}

//日付と場所に基づいたレース情報
function getTwoFilterQuery($racedate_filter,$race_filter){
    $sql= "SELECT RACE_ID,
                    RACEDATE,
                    RNAME,
                    RACENUMBER,
                    TIME,
                    DISTANCE,
                    GROUND,
                    PLACE,
                    WEATHER 
                FROM RACE
                WHERE RACEDATE = $racedate_filter
                AND PLACE = $race_filter
                ORDER BY RACENUMBER ASC"; 
    return $sql;
}

//日付のプルダウン
function getRacedatePulldown(){
   $sql = "SELECT DISTINCT
                    RACEDATE
                FROM RACE 
                ORDER BY RACEDATE DESC;";
    return $sql;
}

//場所のプルダウン
function getPlacePulldown(){
    $sql = "SELECT DISTINCT
                     PLACE
                 FROM RACE 
                 ORDER BY RACEDATE DESC;";
     return $sql;
 }


 //初回接続時の最新日付の場所
 function getPlaceFirst(){
    $sql = "SELECT DISTINCT PLACE 
    FROM RACE 
    WHERE RACEDATE = (
        SELECT MAX(RACEDATE) FROM RACE
    )
    ORDER BY ;";
    return $sql;
 }

 //日付に基づいた場所
 function getPlace($racedate_filter){
    $sql = "SELECT DISTINCT PLACE 
    FROM RACE 
    WHERE RACEDATE = $racedate_filter";
    return $sql;
 }

 //HITテーブルを全取得
 function getHit(){
    $sql = "SELECT * FROM HIT";
    return $sql;
 }

 //初回接続時の最新日付レース数をカウント
 function getRacecountFirst(){
    $sql = "SELECT RACEDATE, COUNT(*) as racecount
            FROM RACE
             WHERE RACEDATE = (
                SELECT MAX(RACEDATE) FROM RACE
            );";
    return $sql;
 }

 //日付に基づいたレース数をカウント
 function getRacecount($racedate_filter){
    $sql = "SELECT RACEDATE, COUNT(*) as racecount
                FROM RACE
                WHERE RACEDATE = $racedate_filter;"; 
    return $sql;
 }

 //初回接続時 最新日付の一着的中数をカウント
 function getHitcountFirst(){
    $sql= "SELECT RACE.RACEDATE, COUNT(*) as hitcount
            FROM RACE RACE
            JOIN RESULT_HORSE RESULT
            ON RACE.RACE_ID = RESULT.RACE_ID
            JOIN PREDICTION_HORSE PREDICTION
            ON RACE.RACE_ID = PREDICTION.RACE_ID
            WHERE RACEDATE = (
                SELECT MAX(RACEDATE) FROM RACE
            )
            AND RESULT.HORSENUMBER = PREDICTION.HORSENUMBER
            AND RESULT.RANKING = '1' AND PREDICTION.RANKING = '1';";
    return $sql;
 }

 //日付に基づいた一着的中数をカウント
 function getHitcount($racedate_filter){
    $sql = "SELECT RACE.RACEDATE, COUNT(*) as hitcount
            FROM RACE RACE
            JOIN RESULT_HORSE RESULT
            ON RACE.RACE_ID = RESULT.RACE_ID
            JOIN PREDICTION_HORSE PREDICTION
            ON RACE.RACE_ID = PREDICTION.RACE_ID
            WHERE RACEDATE = $racedate_filter
            AND RESULT.HORSENUMBER = PREDICTION.HORSENUMBER
            AND RESULT.RANKING = '1' AND PREDICTION.RANKING = '1';";
    return $sql;
}

function getRacedateFirst(){
    $sql = "SELECT DISTINCT SUBSTR(RACEDATE,INSTR(RACEDATE,'年')+1) AS DATE
            FROM RACE
            WHERE RACEDATE = (
                SELECT MAX(RACEDATE)
                FROM RACE
            );";
    return $sql;
}

function getRacedate($racedate_filter){
    $sql = "SELECT DISTINCT SUBSTR(RACEDATE,INSTR(RACEDATE,'年')+1) AS DATE
            FROM RACE
            WHERE RACEDATE = $racedate_filter;";
    return $sql;
}

//初回接続時 最新日付の単勝的中した払い戻しを合計する
function getCollectFirst(){
    $sql = "SELECT SUM(HIT.BETBACK) as collect
            FROM RACE RACE
            JOIN RESULT_HORSE RESULT
            ON RACE.RACE_ID = RESULT.RACE_ID
            JOIN PREDICTION_HORSE PREDICTION
            ON RACE.RACE_ID = PREDICTION.RACE_ID
            JOIN HIT_DETAIL HIT
            ON RACE.RACE_ID = HIT.  RACE_ID
            WHERE RACEDATE = (
                SELECT MAX(RACEDATE) FROM RACE
            )
            AND RESULT.HORSENUMBER = PREDICTION.HORSENUMBER
            AND PREDICTION.HORSENUMBER = HIT.HORSENUMBER 
            AND HIT.HORSENUMBER NOT LIKE '%-%' AND HIT.HORSENUMBER NOT LIKE '%>%'
            AND RESULT.RANKING = '1' AND PREDICTION.RANKING = '1'
            AND HIT.KINDS = '単勝';";
    return $sql;
}

//日付に基づいた単勝的中した払い戻しを合計する
function getCollect($racedate_filter){
    $sql = "SELECT SUM(HIT.BETBACK) as collect
            FROM RACE RACE
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
    return $sql;
}

/*
    的中しているか判定する
*/

function getHitCheck($race_filter,$racedate_filter){
    $sql = "SELECT R.HNAME AS RESULT_NAME, P.HNAME AS PREDICTION_NAME
            FROM RESULT_HORSE R
            JOIN PREDICTION_HORSE P
            ON R.RACE_ID = P.RACE_ID
            WHERE R.RACE_ID IN (
                SELECT RACE.RACE_ID 
                FROM RACE RACE
                WHERE RACE.RACEDATE = $racedate_filter
                AND RACE.PLACE = $race_filter
            )
            AND R.RANKING = '1'
            AND P.RANKING = 1;";
    return $sql;
}

/*
    人気ランキング
*/
function getStrongRanking(){
    $sql = "SELECT HNAME, COUNT(*) AS COUNT
            FROM RESULT_HORSE
            WHERE RANKING = '1'
            GROUP BY HNAME
            ORDER BY COUNT DESC 
            LIMIT 3";
    return $sql;
}

?>