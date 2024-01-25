<?php
include('def.php');
include('db.php');
include('functions.php');
$sql_race = "";


if (isset($_GET['selectedValue'])) {
    $selectedValue = $_GET['selectedValue'];
} else {
    $selectedValue = '';
}

# 場所
if (array_key_exists('raceplace', $_GET)) {
    $x = $_GET['raceplace'];
    $race_filter = "'$x'";
} else {
    $_GET['raceplace'] = "";
    $race_filter = "";
}

# 日付
if (array_key_exists('racedate', $_GET)) {
    $y = $_GET['racedate'];
    $racedate_filter = "'$y'";
} else {
    $_GET['racedate'] = "";
    $racedate_filter = "";
}


/*
    Raceテーブルからindex.phpに表示するデータをreturnする関数
*/


try {
    $db = db_connect();

    function getElement($db, $sql)
    {
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = [];
        while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $rows;
        }
        return $result;
    }
    if (strcmp($race_filter, "") == 0 || strcmp($racedate_filter, "") == 0) {
        $sql_maxrace = getMaxRaceQuery();
        $race_filter_result = getElement($db, $sql_maxrace);
        $stmt = null;
        $racedate_filter = "'" . $race_filter_result[0]["RACEDATE"] . "'";
        $race_filter = "'" . $race_filter_result[0]["PLACE"] . "'";
    }
    $sql_race = getTwoFilterQuery($racedate_filter, $race_filter);
    $result_race = getElement($db, $sql_race);
    $stmt = null;

    //placeとracedateの両方の条件が満たされない場合
    if (empty($result_race)) {
        //日付を確認
        $sql_race = getOneFilterQuery($racedate_filter);
        $result_race = getElement($db, $sql_race);
        $stmt = null;
        //日付を確認した上で、その日付が存在しなかったら
        if (empty($result_race)) {
            $sql_maxrace = getMaxRaceQuery();
            $race_filter_result = getElement($db, $sql_maxrace);
            $stmt = null;
            $racedate_filter = "'" . $race_filter_result[0]["RACEDATE"] . "'";
            $race_filter = "'" . $race_filter_result[0]["PLACE"] . "'";

            $sql_race = getTwoFilterQuery($racedate_filter, $race_filter);
            $result_race = getElement($db, $sql_race);
            $stmt = null;
        }
        # その場所のレースのみを取得する
        if (count($result_race) > 11) {
            $race_filter = "'" . $result_race[0]["PLACE"] . "'";
            $sql_race = getTwoFilterQuery($racedate_filter, $race_filter);
            $result_race = getElement($db, $sql_race);
            $stmt = null;
        }
    }

    /*
        Raceテーブルからindex.phpに日付をプルダウン形式で表示するデータ
    */

    $sql_date = getRacedatePulldown();
    $result_race_date = getElement($db, $sql_date);
    $stmt = null;

    /*
        Raceテーブルからindex.phpに場所をプルダウン形式で表示するデータ
    */

    $sql_place = getPlacePulldown();
    $result_race_place = getElement($db, $sql_place);
    $stmt = null;

    /*
        Raceテーブルからindex.phpの開催場を表示するデータをreturnする関数
    */

    if (strcmp($racedate_filter, "") == 0) {
        $sql_race_place_first = getPlaceFirst();
        $result_race_place = getElement($db, $sql_race_place_first);
        $stmt = null;
    } else {
        $sql_race_place = getPlace($racedate_filter);
        $result_race_place = getElement($db, $sql_race_place);
        $stmt = null;
    }

    /*
        Hitテーブルからindex.phpに表示するデータをreturnする関数
    */

    $sql_hit = getHit();
    $result_hit = getElement($db, $sql_hit);
    $stmt = null;

    /*
        一日に開催されたレースをカウントする関数
    */

    if (strcmp($race_filter, "") == 0 && strcmp($racedate_filter, "") == 0) {
        $sql_racecount_first = getRacecountFirst();
        $result_racecount = getElement($db, $sql_racecount_first);
        $stmt = null;
        $racedate_filter = "'" . $result_racecount[0]["RACEDATE"] . "'";
    }
    $sql_racecount = getRacecount($racedate_filter);
    $result_racecount = getElement($db, $sql_racecount);
    $stmt = null;

    /*
        日付ごとの単勝的中数を数える関数
    */

    if (strcmp($race_filter, "") == 0 && strcmp($racedate_filter, "") == 0) {
        $sql_hitcount_first = getHitcountFirst();
        $result_hitcount_first = getElement($db, $sql_hitcount_first);
        $stmt = null;
        $racedate_filter = "'" . $result_hitcount_first[0]["RACEDATE"] . "'";
    }
    $sql_hitcount = getHitcount($racedate_filter);
    $result_hitcount = getElement($db, $sql_hitcount);
    $stmt = null;

    /*
        回収率・的中率表示部分の日付取得
    */

    if (strcmp($race_filter, "") == 0 && strcmp($racedate_filter, "") == 0) {
        $sql_collecthit_racedate_first = getRacedateFirst();
        $result_collecthit_racedate_first = getElement($db, $sql_collecthit_racedate_first);
        $stmt = null;
        $racedate_filter = "'" . $result_collecthit_racedate_first[0]["RACEDATE"] . "'";
    }
    $sql_collecthit_racedate = getRacedate($racedate_filter);
    $result_collecthit_racedate = getElement($db, $sql_collecthit_racedate);
    $stmt = null;

    /*
        回収率を求める関数
    */

    if (strcmp($race_filter, "") == 0 && strcmp($racedate_filter, "") == 0) {
        $sql_collect_first = getCollectFirst();
        $result_collect_first = getElement($db, $sql_collect_first);
        $stmt = null;
        $racedate_filter = "'" . $result_collect_first[0]["RACEDATE"] . "'";
    }
    $sql_collect = getCollect($racedate_filter);
    $result_collect = getElement($db, $sql_collect);
    $stmt = null;


    /*
        的中orはずれを判定する関数
    */

    $sql_hitcheck = getHitCheck($race_filter, $racedate_filter);
    $result_hitcheck = getElement($db, $sql_hitcheck);

    if (empty($result_hitcheck)) {
        $result_hitcheck[0]['RESULT_NAME'] = "";
    }
    $stmt = null;

    /*
        最強馬ランキング(一位の回数をカウント)
    */

    $sql_StrongRanking = getStrongRanking();
    $result_StrongRanking = getElement($db, $sql_StrongRanking);

    /*
        人気ランキング(一番人気の回数をカウント)
    */
    $sql_PopularRanking = getPopularRanking();
    $result_PopularRanking = getElement($db, $sql_PopularRanking);

    /*
        直近のG1レース(最新の日付から5レース取得)
    */

    $sql_G1Race = getG1Race();
    $result_G1Race = getElement($db, $sql_G1Race);




} catch (PDOException $poe) {
    echo $sql_race;
    $db->rollBack();
    var_dump($race_filter);
    exit("DBエラー" . $poe->getMessage());

} finally {
    $stmt = null;
    $db = null;
}
?>