<?php
//db.phpに接続
// include('/php/db.php');
include('../php/db.php');

$ua = $_SERVER['HTTP_USER_AGENT'];

try {
    //データベース接続
    $db = db_connect();
    //もしrace_idが存在しなければ、index.phpに戻る
    if (isset($_GET['race_id'])) {

        $race_id = $_GET['race_id'];

        //文字数制限
        $Length = 12;

        //文字列の確認
        if (mb_strlen($race_id) !== $Length) {
            header("Location: race_result.php");
        }

        // 数字以外の文字を排除
        if (!ctype_digit($race_id)) {
            header("Location: race_result.php");
            exit();
        }

        //データベースの存在チェック
        $stmt = $db->prepare("SELECT COUNT(*)AS COUNT FROM RACE WHERE race_id = :race_id");
        $stmt->bindParam(':race_id', $race_id, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        header("Location: race_result.php");
        exit();
    }

    function getElement($db, $race_id, $sql)
    {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":race_id", $race_id);
        $stmt->execute();
        $result = [];
        while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $rows;
        }
        return $result;
    }

    function getElementRanking($db, $sql)
    {
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = [];
        while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $rows;
        }
        return $result;
    }






    /*
        race_idに基づいて、レース結果を表示
    */
    $sql_race_result = "SELECT R.HORSENUMBER,
                                R.HORSEFRAME,
                                R.HNAME,
                                R.HORSE_ID,
                                R.GENDER,
                                R.AGE,
                                R.JOCKEY,
                                R.JOCKEY_ID,
                                R.HORSE_WEIGHT,
                                R.WEIGHT_GAIN_LOSS,
                                R.ODDS,
                                R.POPULAR,
                                R.RANKING AS RESULT_RANKING,
                                P.RANKING  AS PREDICTION_RANKING
                        FROM RESULT_HORSE R
                        JOIN PREDICTION_HORSE P
                        ON R.RACE_ID = P.RACE_ID
                        WHERE R.RACE_ID = :race_id
                        AND R.HNAME = P.HNAME
                        ORDER BY R.HORSENUMBER ASC";
    $result_race_result = getElement($db, $race_id, $sql_race_result);
    //jockey_idにidが存在しない場合
    // if (strcmp($result_race_result['JOCKEY_ID'], "") === 0) {
    //     $result_race_result['HORSE_ID'] = ;
    // }
    $stmt = null;

    /*
        race_idを参照して、払い戻しを詳細を表示するデータ
    */

    $sql_hit_detail = "SELECT * FROM HIT_DETAIL WHERE RACE_ID = :race_id";
    $result_hit_detail = getElement($db, $race_id, $sql_hit_detail);
    $stmt = null;

    /*
        race_idを参照して、レース情報を表示するデータ
    */
    $sql_race = "SELECT
                    RACEDATE,
                    RNAME,
                    RACENUMBER,
                    TIME,
                    DISTANCE,
                    HORSE_TOTAL,
                    GROUND,
                    PLACE,
                    WEATHER,
                    SPIN,
                    SITUATION
    FROM RACE
    WHERE RACE_ID = :race_id";
    $result_race_detail = getElement($db, $race_id, $sql_race);
    $stmt = null;

    /*
        race_idを参照して、レース詳細の色を識別するデータ
    */

    $sql_horsenumber_judge = "SELECT
                            HORSENUMBER,
                            HORSEFRAME
    FROM RESULT_HORSE
    WHERE RACE_ID = :race_id";
    $result_detail_judge = getElement($db, $race_id, $sql_horsenumber_judge);
    $stmt = null;

    /*
        的中しているかを判断する
    */
    $sql_hitjudge = "SELECT R.HNAME AS resultNo1Horse, P.HNAME AS predictionNo1Horse
                     FROM RESULT_HORSE R
                     JOIN PREDICTION_HORSE P
                     ON R.RACE_ID = P.RACE_ID
                     WHERE R.RACE_ID = :race_id
                     AND R.RANKING = '1'
                     AND P.RANKING = '1';";
    $result_hitjudge = getElement($db, $race_id, $sql_hitjudge);
    if (empty($result_hitjudge)) {
        $result_hitjudge[0]['resultNo1Horse'] = "";
    }
    $stmt = null;

    /*
        過去最高額（単勝）
    */

    $sql_BetbackSingle = "SELECT H.RACE_ID,
                                R.PLACE,
                                R.RNAME,
                                R.RACEDATE,
                                H.BETBACK
                        FROM HIT_DETAIL H
                        JOIN RACE R
                        ON H.RACE_ID = R.RACE_ID
                        WHERE H.KINDS = '単勝'
                        ORDER BY CAST(REPLACE(H.BETBACK, ',', '') AS SIGNED) DESC
                        LIMIT 1;";
    $result_BetbackSingle = getElementRanking($db, $sql_BetbackSingle);
    $stmt = null;

    /*
        過去払戻し最高額（3連単）
    */

    $sql_BetbackTriple = "SELECT H.RACE_ID,
                            R.PLACE,
                            R.RNAME,
                            R.RACEDATE,
                            H.BETBACK
                    FROM HIT_DETAIL H
                    JOIN RACE R
                    ON H.RACE_ID = R.RACE_ID
                    WHERE H.KINDS = '3連単'
                    ORDER BY CAST(REPLACE(H.BETBACK, ',', '') AS SIGNED) DESC
                    LIMIT 1;";
    $result_BetbackTriple = getElementRanking($db, $sql_BetbackTriple);
    $stmt = null;





} catch (Exception $e) {
    exit("DBエラー :" . $e->getMessage());
} finally {
    $stmt = null;
    $db = null;
}

/*
    渡された馬枠の数字によって色を変えて
    cssのidをreturnする
*/
function getBgclass($find_judge)
{
    $bgclass = ""; //初期クラス

    if ($find_judge == 1) {
        $bgclass = 'bg-white'; //白
    } elseif ($find_judge == 2) {
        $bgclass = 'bg-black'; //黒
    } elseif ($find_judge == 3) {
        $bgclass = 'bg-red'; //赤
    } elseif ($find_judge == 4) {
        $bgclass = 'bg-blue'; //青
    } elseif ($find_judge == 5) {
        $bgclass = 'bg-yellow'; //黄色
    } elseif ($find_judge == 6) {
        $bgclass = 'bg-green'; //緑
    } elseif ($find_judge == 7) {
        $bgclass = 'bg-orange'; //橙
    } elseif ($find_judge == 8) {
        $bgclass = 'bg-pink'; //桃
    }
    return $bgclass;
}


/*
    オッズが10.0以下ならば文字色を変える
*/
function getOddsColor($odds)
{
    $bgodds = '';
    $odds = strval($odds);
    if ($odds < '10.0') {
        $bgodds = 'bgodds';
    }
    return $bgodds;
}

/*
    １番人気には黄色、２番人気には青、３番人気には赤の
    背景色をつける（人気・予想着順はINT型、確定着順はString型）
*/

function getBgRankingString($ranking)
{
    $bgranking = ''; //初期値
    if ($ranking == '1') {
        $bgranking = 'bg-ranking-yellow';
    } elseif ($ranking == '2') {
        $bgranking = 'bg-ranking-blue';
    } elseif ($ranking == '3') {
        $bgranking = 'bg-ranking-red';
    }
    return $bgranking;
}

function getBgRankingInt($ranking)
{
    $bgranking = ''; //初期値
    if ($ranking == 1) {
        $bgranking = 'bg-ranking-yellow';
    } elseif ($ranking == 2) {
        $bgranking = 'bg-ranking-blue';
    } elseif ($ranking == 3) {
        $bgranking = 'bg-ranking-red';
    }
    return $bgranking;
}

function getWeight($weight)
{
    $horse_GainLoss = '';
    if ($weight > 0) {
        $horse_GainLoss = '+' . $weight;
    } elseif ($weight == 0) {
        $horse_GainLoss = '±' . $weight;
    } else {
        $horse_GainLoss = $weight;
    }
    return $horse_GainLoss;
}


/* 天気マーク関数 */
function getWeather($weather)
{
    $icon = '';
    switch ($weather) {
        case '晴':
            $icon = 'tennki-illust1.png';
            break;
        case '曇':
            $icon = 'tennki-illust5.png';
            break;
        case '雨':
            $icon = 'tennki-illust7.png';
            break;
        case '小雨':
            $icon = 'tennki-illust17.png';
            break;
        case '雪':
        case '小雪':
            $icon = 'tennki-illust18.png';
            break;
        default:
            break;
    }

    if ($icon !== '') {
        echo ('<img class="weather_icon" src="./img/' . $icon . '" alt="準備中" width="30px" height="30px">');
    }
}

//馬番の色を分ける関数
function getColorJudge($colorHorsenumber, $result_detail_judge)
{
    $find = '';
    foreach ($result_detail_judge as $result_judge) {
        if ($colorHorsenumber == $result_judge['HORSENUMBER']) {
            $find = $result_judge['HORSEFRAME'];
        }
    }
    $bgclass = getBgclass($find);

    return $bgclass;
}

/*
    「芝」or「ダート」or「障害」で背景色を切り替える関数
*/
function getBgRaceNumber($ground)
{
    $bgground = '';
    if ($ground == 'ダート') {
        $bgground = 'BgGroundBrown';
    } elseif ($ground == '芝') {
        $bgground = 'BgGroundGreen';
    } elseif ($ground == '障害') {
        $bgground = 'BgGroundOrange';
    }
    return $bgground;
}



?>
<!doctype html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ウマ男爵 - レース詳細</title>
    <link rel="stylesheet" media="all" href="./css/ress.min.css" />
    <link rel="stylesheet" media="all" href="./css/style.css" />
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/style.js"></script>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="./img/favicon.png">

</head>

<body>
    <!-- headerの読み込み -->
    <?php require_once("./component/header.php") ?>

    <div class='top-mainimg'>
        <div class="mainimg">
            <h1 id="date">
                <?= $result_race_detail[0]['RACEDATE'] ?>のAI予想結果
            </h1>
        </div>
    </div>

    <main>
        <article>
            <div class="col span-12">
                <div class='background'>
                    <div class="container_result">
                        <div class="row">
                            <div class="breadcrumb">
                                <ul>
                                    <li><a href="race_list.php">競馬予想</a> > 詳細ページ</li>
                                </ul>
                                <div class='race_detail'>
                                    <?php foreach ($result_race_detail as $race_detail): ?>
                                        <div class='race_infomation'>
                                            <!-- <div class = 'race_infomation'> -->
                                            <h3>
                                                <div class='race_name-top'>
                                                    <div class='race_name-top-left'>
                                                        <?php
                                                        $bgRaceNumber = getBgRaceNumber($race_detail['GROUND']);
                                                        echo '<span id = "' . $bgRaceNumber . '">' . $race_detail["RACENUMBER"] . 'R</span>&nbsp;';
                                                        echo $race_detail['RNAME'];
                                                        ?>
                                                    </div>
                                                    <div class='race_name-top-right'>
                                                        <?php
                                                        if (strcmp($result_hitjudge[0]['resultNo1Horse'], "") != 0) {
                                                            if ($result_hitjudge[0]['resultNo1Horse'] == $result_hitjudge[0]['predictionNo1Horse']) {

                                                                echo '<img class="hit_icon-subpage" src="./img/的中.png" alt="準備中">';

                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </h3>
                                            <p>
                                                <?= $race_detail['PLACE'] ?> /
                                                <?= $race_detail['TIME'] ?>発走 /
                                                天気：
                                                <?php
                                                $weather = $race_detail['WEATHER'];
                                                getWeather($weather);
                                                ?> /
                                                <?= $race_detail['GROUND'] ?>
                                                <?= $race_detail['DISTANCE'] ?>m (
                                                <?= $race_detail['SPIN'] ?>) /
                                                頭数：
                                                <?= $race_detail['HORSE_TOTAL'] ?>頭 /
                                                馬場：
                                                <?= $race_detail['SITUATION'] ?>
                                            </p>
                                            <!-- </div> -->
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            </div>




                            <?php
                            $ua = $_SERVER['HTTP_USER_AGENT'];
                            if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false)): ?>

                                <!-- スマホの場合 -->
                                <div class='result'>
                                    <div class="table-title">
                                        <h3 class="cp_h3title">出馬表</h3>
                                    </div>
                                    <table>
                                        <tr>
                                            <th class='r result_horsenumber c'>馬枠</th>
                                            <th class='r result_horsenumber c'>馬番</th>
                                            <th class='r result_hname c'>馬名</th>
                                            <th class='r result_odds c'>オッズ</th>
                                            <th class='r result_popular c'>人気</th>
                                            <th class='r result_ranking c'>AI<br>予想</th>
                                            <th class='r prediction_ranking c'>確定着順</th>
                                        </tr>
                                        <?php foreach ($result_race_result as $result_race): ?>
                                            <tr>
                                                <td class='r result_horseframe'>
                                                    <?= $result_race['HORSEFRAME'] ?>
                                                </td>
                                                <?php
                                                $bgResult = getBgRankingString($result_race['RESULT_RANKING']);
                                                $bgPopular = getBgRankingInt($result_race['POPULAR']);
                                                $bgPrediction = getBgRankingInt($result_race['PREDICTION_RANKING']);
                                                $bgOdds = getOddsColor($result_race['ODDS']);
                                                $bgclass = getColorJudge($result_race['HORSENUMBER'], $result_detail_judge);
                                                echo '<td class = "r result_horsenumber bg-all" id = "' . $bgclass . '">' . $result_race['HORSENUMBER'] . '</td>';
                                                ?>
                                                <td class='r result_hname'>
                                                    <?php
                                                    echo '<a href = "https://db.netkeiba.com/horse/' . $result_race['HORSE_ID'] . '"?rf=top_pickup">' . $result_race['HNAME'] . '</a>';
                                                    ?>
                                                </td>
                                                <?php
                                                // オッズ
                                                if (strcmp($result_race['ODDS'], "0")) {
                                                    if (false === strpos($result_race['ODDS'], ".")) {
                                                        $result_race['ODDS'] = $result_race['ODDS'] . ".0";
                                                    }
                                                    echo '<td class = "r result_odds" id = "' . $bgPrediction . '"><span id = "' . $bgOdds . '">' . $result_race['ODDS'] . '</span></td>';
                                                } else {
                                                    echo '<td class = "r result_odds" id = "' . $bgPrediction . '"> - </td>';
                                                }
                                                // 人気
                                                if (strcmp($result_race['POPULAR'], "0")) {
                                                    echo '<td class = "r result_popular" id = "' . $bgPopular . '">' . $result_race['POPULAR'] . '</td>';
                                                } else {
                                                    echo '<td class = "r result_popular" id = "' . $bgPopular . '"> - </td>';
                                                }
                                                // 予想着順
                                                echo '<td class = "r result_ranking" id = "' . $bgPrediction . '">' . $result_race['PREDICTION_RANKING'] . '着</td>';
                                                $output = "";
                                                // 確定着順
                                                if (strcmp($result_race['RESULT_RANKING'], '失格') == 0) {
                                                    $output = "失格";
                                                } elseif (strcmp($result_race['RESULT_RANKING'], '取消') == 0) {
                                                    $output = "取消";
                                                } elseif (strcmp($result_race['RESULT_RANKING'], '除外') == 0) {
                                                    $output = "除外";
                                                } elseif (strcmp($result_race['RESULT_RANKING'], '中止') == 0) {
                                                    $output = "中止";
                                                } elseif (strcmp($result_race['RESULT_RANKING'], '未定') == 0) {
                                                    $output = "-";
                                                } else {
                                                    $output = $result_race['RESULT_RANKING'] . "着";
                                                }
                                                echo '<td class = "r prediction_ranking" id = "' . $bgResult . '">' . $output . '</td>';
                                                ?>
                                            </tr>
                                        <?php endforeach ?>
                                    </table>
                                </div>
                            <?php else: ?>

                                <!-- PCの場合 -->
                                <div class='result'>
                                    <div class="horse-title">
                                        <h3 class="cp_h3title">出馬表</h3>
                                    </div>
                                    <table>
                                        <tr>
                                            <th class='r result_horsenumber c'>馬枠</th>
                                            <th class='r result_horsenumber c'>馬番</th>
                                            <th class='r result_hname c'>馬名</th>
                                            <th class='r result_gender c'>性齢</th>
                                            <th class='r result_horse_weight c' id="pc_only">馬体重<br>(kg)</th>
                                            <th class='r result_jockey c'>騎手</th>
                                            <th class='r result_odds c' id="odds">オッズ</th>
                                            <th class='r result_popular c'>人気</th>
                                            <th class='r result_ranking c'>AI<br>予想</th>
                                            <th class='r prediction_ranking c'>確定<br>着順</th>
                                        </tr>
                                        <?php foreach ($result_race_result as $result_race): ?>
                                            <tr>
                                                <td class='r result_horseframe'>
                                                    <?= $result_race['HORSEFRAME'] ?>
                                                </td>
                                                <?php
                                                $bgResult = getBgRankingString($result_race['RESULT_RANKING']);
                                                $bgPopular = getBgRankingInt($result_race['POPULAR']);
                                                $bgPrediction = getBgRankingInt($result_race['PREDICTION_RANKING']);
                                                $bgOdds = getOddsColor($result_race['ODDS']);
                                                $horse_GainLoss = getWeight($result_race['WEIGHT_GAIN_LOSS']);
                                                $bgclass = getColorJudge($result_race['HORSENUMBER'], $result_detail_judge);
                                                echo '<td class = "r result_horsenumber bg-all" ><span class = "bg-all" id = "' . $bgclass . '">' . $result_race['HORSENUMBER'] . '</span></td>';
                                                ?>
                                                <td class='r result_hname'>
                                                    <?php
                                                    echo '<a href = "https://db.netkeiba.com/horse/' . $result_race['HORSE_ID'] . '"?rf=top_pickup"><span>' . $result_race['HNAME'] . '</span></a>';
                                                    ?>
                                                </td>
                                                <?php
                                                echo '<td class = "r result_gender">' . $result_race['GENDER'] . '' . $result_race['AGE'] . '</td>';
                                                ?>

                                                <?php
                                                // 馬体重
                                                if (strcmp($result_race['HORSE_WEIGHT'], "0")) {
                                                    echo '<td class = "r result_horse_weight" id = "pc_only">' . $result_race["HORSE_WEIGHT"] . '(' . $horse_GainLoss . ')</td>';
                                                } else {
                                                    echo '<td class = "r result_horse_weight" id = "pc_only"> ---  </td>';
                                                }
                                                // 騎手
                                                if (empty($result_race['JOCKEY_ID'])) {
                                                    echo '<td class = "r result_jockey"><span>' . $result_race['JOCKEY'] . '</span></td>';
                                                } else {
                                                    echo '<td class = "r result_jockey"><a href = "https://db.netkeiba.com/jockey/result/recent/' . $result_race['JOCKEY_ID'] . '/"><span>' . $result_race['JOCKEY'] . '</span></a></td>';

                                                }
                                                // オッズ
                                                if (strcmp($result_race['ODDS'], "0")) {
                                                    if (false === strpos($result_race['ODDS'], ".")) {
                                                        $result_race['ODDS'] = $result_race['ODDS'] . ".0";
                                                    }
                                                    echo '<td class = "r result_odds" id = "' . $bgPrediction . '"><span id = "' . $bgOdds . '">' . $result_race['ODDS'] . '</span></td>';
                                                } else {
                                                    echo '<td class = "r result_odds" id = "' . $bgPrediction . '"> --- </td>';
                                                }
                                                // 人気
                                                if (strcmp($result_race['POPULAR'], "0")) {
                                                    echo '<td class = "r result_popular" id = "' . $bgPopular . '">' . $result_race['POPULAR'] . '人気</td>';
                                                } else {
                                                    echo '<td class = "r result_popular" id = "' . $bgPopular . '"> --- </td>';
                                                }
                                                // 予想着順
                                                echo '<td class = "r result_ranking" id = "' . $bgPrediction . '">' . $result_race['PREDICTION_RANKING'] . '着</td>';
                                                $output = "";
                                                // 確定着順
                                                if (strcmp($result_race['RESULT_RANKING'], '失格') == 0) {
                                                    $output = "失格";
                                                } elseif (strcmp($result_race['RESULT_RANKING'], '取消') == 0) {
                                                    $output = "取消";
                                                } elseif (strcmp($result_race['RESULT_RANKING'], '除外') == 0) {
                                                    $output = "除外";
                                                } elseif (strcmp($result_race['RESULT_RANKING'], '中止') == 0) {
                                                    $output = "中止";
                                                } elseif (strcmp($result_race['RESULT_RANKING'], '未定') == 0) {
                                                    $output = "---";
                                                } else {
                                                    $output = $result_race['RESULT_RANKING'] . "着";
                                                }
                                                echo '<td class = "r prediction_ranking" id = "' . $bgResult . '">' . $output . '</td>';
                                                ?>
                                            </tr>
                                        <?php endforeach ?>
                                    </table>
                                </div>
                            <?php endif ?>




                            <div class="betback-title">
                                <h3 class="cp_h3title">払戻し表</h3>
                            </div>
                            <div class="container_summarize">
                                <!-- <div class='top_hit_detail'> -->
                                <div class='hit_detail'>
                                    <div class='summarize_element'>
                                        <table>
                                            <tr>
                                                <th class='K kinds C'>式別</th>
                                                <th class='K horsenumber horsenumber_title C'>番号</th>
                                                <th class='K betback C'>払戻し</th>
                                                <th class='K popular_betback C'>人気</th>
                                            </tr>
                                            <?php foreach ($result_hit_detail as $hit_detail): ?>
                                                <tr>
                                                    <td class='K kinds'>
                                                        <?= $hit_detail['KINDS'] ?>
                                                    </td>
                                                    <?php
                                                    if ($hit_detail['KINDS'] == 'ワイド') {
                                                        if (preg_match('/-/', $hit_detail['HORSEFRAME'])) {
                                                            $horsenumbers = explode("-", $hit_detail['HORSEFRAME']);
                                                            //[1 , 3]
                                                            // var_dump($horsenumbers);
                                                
                                                            $horse_total = count($horsenumbers);
                                                            $horse_count = 0;

                                                            for ($i = 0; $i <= $horse_total; $i++) {
                                                                $find_judge = NULL;

                                                                if ($i !== $horse_total) {
                                                                    for ($j = 0; $j < count($result_detail_judge); $j++) {
                                                                        if ($horsenumbers[$i] == $result_detail_judge[$j]['HORSENUMBER']) {
                                                                            $find_judge = $result_detail_judge[$j]['HORSEFRAME'];
                                                                        }
                                                                    }
                                                                }

                                                                $bgclass = getBgclass($find_judge);

                                                                // echo 'hhhhhh';
                                                                if ($horse_count == 0) {
                                                                    echo '<td class = "bg-all K horsenumber"><span id = "' . $bgclass . '">' . $horsenumbers[$i] . '</span>';

                                                                } else if ($horse_total !== $horse_count) {
                                                                    echo '<span class = "num"> - </span> <span id = "' . $bgclass . '">' . $horsenumbers[$i] . '</span>';

                                                                } else {
                                                                    echo '</td>';

                                                                }
                                                                $horse_count++;
                                                            }
                                                        }
                                                    } else {
                                                        if (preg_match('/-/', $hit_detail['HORSENUMBER'])) {
                                                            $horsenumbers = explode("-", $hit_detail['HORSENUMBER']);
                                                            //[1 , 3]
                                                            // var_dump($horsenumbers);
                                                
                                                            $horse_total = count($horsenumbers);
                                                            $horse_count = 0;

                                                            for ($i = 0; $i <= $horse_total; $i++) {
                                                                $find_judge = NULL;

                                                                if ($i !== $horse_total) {
                                                                    for ($j = 0; $j < count($result_detail_judge); $j++) {
                                                                        if ($horsenumbers[$i] == $result_detail_judge[$j]['HORSENUMBER']) {
                                                                            $find_judge = $result_detail_judge[$j]['HORSEFRAME'];
                                                                        }
                                                                    }
                                                                }

                                                                $bgclass = getBgclass($find_judge);



                                                                if ($horse_count == 0) {
                                                                    echo '<td class = "bg-all K horsenumber"><span id = "' . $bgclass . '">' . $horsenumbers[$i] . '</span>';
                                                                } else if ($horse_total !== $horse_count) {
                                                                    echo '<span class = "num"> - </span> <span id = "' . $bgclass . '">' . $horsenumbers[$i] . '</span>';
                                                                } else {
                                                                    echo '</td>';
                                                                }
                                                                $horse_count++;
                                                            }
                                                        } elseif (preg_match('/>/', $hit_detail['HORSENUMBER'])) {
                                                            $horsenumbers = explode(">", $hit_detail['HORSENUMBER']);
                                                            //[1 , 3]
                                                            // var_dump($horsenumbers);
                                                
                                                            $horse_total = count($horsenumbers);
                                                            $horse_count = 0;

                                                            for ($i = 0; $i <= $horse_total; $i++) {
                                                                $find_judge = NULL;

                                                                if ($i !== $horse_total) {
                                                                    for ($j = 0; $j < count($result_detail_judge); $j++) {
                                                                        if ($horsenumbers[$i] == $result_detail_judge[$j]['HORSENUMBER']) {
                                                                            $find_judge = $result_detail_judge[$j]['HORSEFRAME'];
                                                                        }
                                                                    }
                                                                }

                                                                $bgclass = getBgclass($find_judge);

                                                                if ($horse_count == 0) {
                                                                    echo '<td class = "bg-all K horsenumber"><span id = "' . $bgclass . '">' . $horsenumbers[$i] . '</span>';
                                                                } else if ($horse_total !== $horse_count) {
                                                                    echo '<span class = "num"> > </span> <span id = "' . $bgclass . '">' . $horsenumbers[$i] . '</span>';
                                                                } else {
                                                                    echo '</td>';
                                                                }
                                                                $horse_count++;
                                                            }
                                                        } else {
                                                            $find_judge = NULL;
                                                            for ($i = 0; $i < count($result_detail_judge); $i++) {
                                                                if ($hit_detail['HORSENUMBER'] == $result_detail_judge[$i]['HORSENUMBER']) {
                                                                    $find_judge = $result_detail_judge[$i]['HORSEFRAME'];
                                                                }
                                                            }
                                                            $bgclass = getBgclass($find_judge);
                                                            echo '<td class = "bg-all K horsenumber"><span id = "' . $bgclass . '">' . $hit_detail['HORSENUMBER'] . '</span></td>';
                                                        }
                                                    }
                                                    ?>
                                                    <td class='K betback'>
                                                        <?= $hit_detail['BETBACK'] ?>円
                                                    </td>
                                                    <td class='K popular_betback'>
                                                        <?= $hit_detail['POPULAR'] ?>人気
                                                    </td>
                                                </tr>

                                            <?php endforeach ?>
                                        </table>
                                    </div>
                                    <div class='hit_detail-sidevar'>
                                        <div class='hit_detail-ranking'>
                                            <h3>過去払戻し最高額（単勝）</h3>
                                            <?php
                                            echo '<p class = "single">' . $result_BetbackSingle[0]['RACEDATE'] . '<br>「' . $result_BetbackSingle[0]['RNAME'] . '」<br><span class = "betback-Emphasis">' . $result_BetbackSingle[0]['BETBACK'] . '</span>円</p>';
                                            echo '<a href = "./subpage.php?race_id=' . $result_BetbackSingle[0]['RACE_ID'] . '" class="btn_03">レース詳細画面へ</a>'
                                                ?>

                                        </div>
                                        <div class='hit_detail-ranking'>
                                            <h3>過去払戻し最高額（3連単）</h3>
                                            <?php
                                            echo '<p class = "triple">' . $result_BetbackTriple[0]['RACEDATE'] . '<br>「' . $result_BetbackTriple[0]['RNAME'] . '」<br><span class = "betback-Emphasis">' . $result_BetbackTriple[0]['BETBACK'] . '</span>円</p>';
                                            echo '<a href = "./subpage.php?race_id=' . $result_BetbackTriple[0]['RACE_ID'] . '" class="btn_03">レース詳細画面へ</a>'
                                                ?>
                                        </div>
                                    </div>

                                </div>
                                <!-- </div> -->
                            </div>
                        </div>
                    </div>
                </div>
        </article>
    </main>

    <!-- フッターの読み込み -->
    <?php require_once("./component/footer.php") ?>
    <p id="pagetop"><a href="#">TOP</a></p>
    <!--自作のJS-->
    <script src="./js/4-1-2.js"></script>
</body>

</html>
