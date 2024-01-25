<?php
include('../php/index_call.php');
$ua = $_SERVER['HTTP_USER_AGENT'];
$hit = ROUND($result_hitcount[0]["hitcount"] / $result_racecount[0]["racecount"] * 100, 1);
$race_money = $result_racecount[0]["racecount"] * 100;
$collect = ROUND(intval($result_collect[0]['collect']) / $race_money * 100, 1);
$count = 0;
$racecount = 0;
$flag = true;

/*
    「RNAME」の文字数を削減
*/

function truncateString($inputString, $maxLength = 10)
{
    if (mb_strlen($inputString) > $maxLength) {
        $truncatedString = mb_substr($inputString, 0, $maxLength - 1) . '...';
        return $truncatedString;
    } else {
        return $inputString;
    }
}

if (strcmp($_GET["racedate"], "")) {
    $position = 680;
} else {
    $position = 0;
}

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
            $icon = 'snow.png';
            break;
        default:
            break;
    }


    if ($icon !== '') {
        echo ('<img class="weather_icon" src="../img/' . $icon . '" alt="準備中" width="25px" height="25px">');
    }
}
?>
<!-- ページの自動スクロール -->
<script>
    window.onload = function () { $(window).scrollTop(<?php echo $position; ?>); }
</script>

<!doctype html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ウマ男爵 - 予想一覧</title>
    <link rel="stylesheet" media="all" href="../css/ress.min.css" />
    <link rel="stylesheet" media="all" href="../css/style.css" />
    <script src="../js/jquery-3.6.0.min.js"></script>
    <script src="../js/style.js"></script>
    <script src="../js/index_get.js"></script>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../img/favicon.png">

    <!-- ローディング画面 -->
    <link rel="stylesheet" type="text/css" href="../css/4-1-2.css">
</head>

<body>

    <div id="splash">
        <div id="splash_text"></div>
        <!--/splash-->
    </div>

    <!-- headerの読み込み -->
    <?php require_once("./component/header.php") ?>

    <?php if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false)): ?>
        <div class='top-mainimg'>
            <div class="mainimg">
                <p>中央競馬レース予想</p>
                <p>全10開催場</p>
            </div>
        </div>
    <?php else: ?>
        <div class='top-mainimg'>
            <div class="mainimg">
                <h3>Let's try predicting horse racing using the uma_dansyaku!</h3>
                <p>開発者4人で力を合わせ競馬予想AIシステムを作りました!</p>
                <p>競馬初心者や競馬予想の参考が欲しい人におすすめ!</p>
            </div>
        </div>
    <?php endif; ?>
    <main>
        <section id="main">
            <div class="container">
                <div class='misosiru'>
                    <?php
                    if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false)): ?>
                    <?php else: ?>
                        <h2 id=race class="catch">中央競馬レース予想(全10開催場)</h2>
                        <div class='race_head'>
                        <?php endif; ?>
                        <form action="race_list.php" id="myform" method="GET">
                            <div id="select_tab">
                                <label class="selectbox-001">
                                    <select id='pulldown_racedate' name='racedate' onchange="submit(this.form)"
                                        onclick="buttonClick()">
                                        <?php foreach ($result_race_date as $x): ?>

                                            <!-- 日付のプルダウンを表示 選択した日付をデフォルトとして表示 -->
                                            <?php if (strcmp($x["RACEDATE"], $result_race[0]["RACEDATE"])) {
                                                echo ("<option value = " . $x["RACEDATE"] . ">" . $x["RACEDATE"] . "</option>");
                                            } else {
                                                echo ("<option value ='" . $x["RACEDATE"] . "'selected>" . $x["RACEDATE"] . "</option>");
                                            }
                                            ?>
                                        <?php endforeach ?>
                                    </select>
                                </label>
                            </div>
                            <div id="select_tab">
                                <label class="selectbox-001">
                                    <select id='pulldown_raceplace' name='raceplace' onchange="submit(this.form)">
                                        <?php foreach ($result_race_place as $x): ?>
                                            <!-- 開催場所のプルダウンを表示 選択した開催場所をデフォルトとして表示 -->
                                            <?php if (strcmp($x["PLACE"], $result_race[0]["PLACE"]) != 0) {
                                                echo ("<option value = " . $x["PLACE"] . ">" . $x["PLACE"] . "</option>");
                                            } else {
                                                echo ("<option value ='" . $x["PLACE"] . "'selected>" . $x["PLACE"] . "</option>");
                                            }
                                            ?>
                                        <?php endforeach ?>
                                    </select>
                                </label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="contents">
                <div id="race_data">
                    <?php for ($i = 0; $i < count($result_race) / 3; $i++): ?>
                        <div class="row">
                            <?php for ($j = 0; $j < 3; $j++): ?> <!-- 3は横並びにする数 -->
                                <?php if ($count == count($result_race)) {
                                    break;
                                } ?>

                                <!-- 場所を指定して絞り込む -->
                                <?php if (strcmp($result_race[$count]["PLACE"], $_GET["raceplace"]) == 0): ?>
                                    <div class="col span-4">
                                        <a href="subpage.php?race_id=<?= $result_race[$count]["RACE_ID"] ?>">
                                            <?php
                                            $output = truncateString($result_race[$count]["RNAME"], 9);
                                            ?>
                                            <h5 class="race_title">
                                                <?= $result_race[$count]["RACENUMBER"] ?>R
                                                <?= $output ?>
                                                <?php
                                                if (strcmp($result_hitcheck[0]['RESULT_NAME'], "") != 0) {
                                                    if ($result_hitcheck[$racecount]['RESULT_NAME'] == $result_hitcheck[$racecount]['PREDICTION_NAME']) {
                                                        echo '<div id = "hit_icon_top"><img class="hit_icon" src="../img/的中.png" alt="準備中"></div>';
                                                    }
                                                }
                                                ?>
                                            </h5>
                                            <p>
                                                <?= $result_race[$count]["TIME"] ?>発走
                                                <?= $result_race[$count]["GROUND"] ?>
                                                <?= $result_race[$count]["DISTANCE"] ?>m 天気 :
                                                <?php
                                                $weather = $result_race[$count]["WEATHER"];
                                                getWeather($weather);
                                                ?>
                                            </p>
                                        </a>
                                    </div>
                                    <?php $flag = false; ?>
                                <?php endif; ?>

                                <!-- 初回接続時 -->
                                <?php if (strcmp($_GET["raceplace"], "") == 0): ?>
                                    <div class="col span-4">
                                        <a href="subpage.php?race_id=<?= $result_race[$count]["RACE_ID"] ?>">
                                            <?php
                                            $output = truncateString($result_race[$count]["RNAME"], 9);
                                            ?>
                                            <h5 class="race_title">
                                                <?= $result_race[$count]["RACENUMBER"] ?>R
                                                <?= $output ?>
                                                <?php
                                                if (strcmp($result_hitcheck[0]['RESULT_NAME'], "") != 0) {

                                                    if ($result_hitcheck[$racecount]['RESULT_NAME'] == $result_hitcheck[$racecount]['PREDICTION_NAME']) {
                                                        echo '<div id = "hit_icon_top"><img class="hit_icon" src="../img/的中.png" alt="準備中"></div>';
                                                    }
                                                }
                                                ?>
                                            </h5>
                                            <p>
                                                <?= $result_race[$count]["TIME"] ?>発走
                                                <?= $result_race[$count]["GROUND"] ?>
                                                <?= $result_race[$count]["DISTANCE"] ?>m 天気：
                                                <?php
                                                $weather = $result_race[$count]["WEATHER"];
                                                getWeather($weather);
                                                ?>
                                            </p>
                                        </a>
                                    </div>
                                    <?php $flag = false; ?>
                                <?php endif; ?>

                                <!-- どれにも該当しなかった場合 -->
                                <?php if ($flag && $count < 12): ?>
                                    <div class="col span-4">
                                        <a href="subpage.php?race_id=<?= $result_race[$count]["RACE_ID"] ?>">
                                            <?php
                                            $output = truncateString($result_race[$count]["RNAME"], 9);
                                            ?>
                                            <h5 class="race_title">
                                                <?= $result_race[$count]["RACENUMBER"] ?>R
                                                <?= $output ?>
                                                <?php
                                                if (strcmp($result_hitcheck[0]['RESULT_NAME'], "") != 0) {
                                                    if ($result_hitcheck[$racecount]['RESULT_NAME'] == $result_hitcheck[$racecount]['PREDICTION_NAME']) {
                                                        echo '<div id = "hit_icon_top"><img class="hit_icon" src="../img/的中.png" alt="準備中"></div>';
                                                    }
                                                }
                                                ?>
                                            </h5>
                                            <p>
                                                <?= $result_race[$count]["TIME"] ?>発走
                                                <?= $result_race[$count]["GROUND"] ?>
                                                <?= $result_race[$count]["DISTANCE"] ?>m 天気：
                                                <?php
                                                $weather = $result_race[$count]["WEATHER"];
                                                getWeather($weather);
                                                ?>
                                            </p>
                                        </a>
                                    </div>
                                <?php endif;
                                $count++;
                                $racecount++; ?>
                            <?php endfor ?>
                        </div>
                    <?php endfor; ?>
                </div>
                <!-- サイドバー -->
                <div id="side_var">
                    <!-- 的中率・回収率を表示する -->
                    <div></div>
                    <div class='description'>
                        <h3>サイトの説明</h3>
                        <p>当サイト<span class='under-bar'>「ウマ男爵」</span>は競馬初心者の方から玄人の方まで幅広いユーザを対象としたWebサイトです。</p>
                        <p>競馬で遊んでみたいけど賭け方が分からない！という方や、最近なかなか勝てなくて...といった方にお勧めのサイトです。</p>
                        <p>私たちは<span class='under-bar'>"回収率100%越え"</span>を目標に、AIの開発、サイトのアップデートを日々行っています。</p>
                    </div>
                    <div class='probability'>
                        <h3>回収率・的中率</h3>
                        <div class='Rate'>
                            <p>回収率</p>
                            <p><span class='font-size-probability'>
                                    <?= $collect ?>
                                </span>%</p>
                        </div>
                        <div class='Rate'>
                            <p>的中率</p>
                            <p><span class='font-size-probability'>
                                    <?= $hit ?>
                                </span>%</p>
                        </div>
                    </div>

                    <div class="side_centense">
                        <h3 class="ranking_title">最強馬ランキング</h3>
                        <?php
                        echo '<div class = "No"><img class="ranking_icon" src="../img/RankingNo.1.png" alt="準備中"><a href = "https://db.netkeiba.com/horse/' . $result_StrongRanking[0]["HORSE_ID"] . '" class = "horseranking"><b>' . $result_StrongRanking[0]["HNAME"] . '</b></a></div>';
                        echo '<div class = "No"><img class="ranking_icon" src="../img/RankingNo.2.png" alt="準備中"><a href = "https://db.netkeiba.com/horse/' . $result_StrongRanking[1]["HORSE_ID"] . '" class = "horseranking">' . $result_StrongRanking[1]["HNAME"] . '</a></div>';
                        echo '<div class = "No"><img class="ranking_icon" src="../img/RankingNo.3.png" alt="準備中"><a href = "https://db.netkeiba.com/horse/' . $result_StrongRanking[2]["HORSE_ID"] . '" class = "horseranking">' . $result_StrongRanking[2]["HNAME"] . '</a></div>';
                        ?>
                    </div>
                    <div class="side_centense">
                        <h3 class="ranking_title">人気馬ランキング</h3>
                        <?php
                        echo '<div class = "No"><img class="ranking_icon" src="../img/RankingNo.1.png" alt="準備中"><a href = "https://db.netkeiba.com/horse/' . $result_PopularRanking[0]["HORSE_ID"] . '" class = "horseranking">' . $result_PopularRanking[0]["HNAME"] . '</a></div>';
                        echo '<div class = "No"><img class="ranking_icon" src="../img/RankingNo.2.png" alt="準備中"><a href = "https://db.netkeiba.com/horse/' . $result_PopularRanking[1]["HORSE_ID"] . '" class = "horseranking">' . $result_PopularRanking[1]["HNAME"] . '</a></div>';
                        echo '<div class = "No"><img class="ranking_icon" src="../img/RankingNo.3.png" alt="準備中"><a href = "https://db.netkeiba.com/horse/' . $result_PopularRanking[2]["HORSE_ID"] . '" class = "horseranking">' . $result_PopularRanking[2]["HNAME"] . '</a></div>';
                        ?>
                    </div>
                    <div class="side_centense">
                        <h3>参考サイト</h3>
                        <div class="icon_banner">
                            <div class="atena">
                                <a href="https://keiba-ai.jp/" class="ATENA_icon"><img src="../img/atenaアイコン.png"
                                        alt="準備中" width="160px" height="160px"> </a>
                            </div>
                            <div class="netkeiba">
                                <a href="https://www.netkeiba.com/" class="netkeiba_icon"><img
                                        src="../img/netkeibaアイコン.png" alt="準備中" width="160px" height="160px"> </a>
                            </div>
                        </div>
                    </div>
                    <div class='g1race'>
                        <h3>直近のG1レース</h3>
                        <?php
                        foreach ($result_G1Race as $g1) {
                            echo '<p class = "g1"><a href = "./subpage.php?race_id=' . $g1['RACE_ID'] . '">' . $g1['YEAR'] . '  ' . $g1['RNAME'] . '</a></p>';
                        }
                        ?>
                    </div>
                    <div>
                    </div>
        </section>
    </main>
    <!-- フッターの読み込み -->
    <?php require_once("./component/footer.php") ?>


    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://rawgit.com/kimmobrunfeldt/progressbar.js/master/dist/progressbar.min.js"></script>
    <!--IE11用-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/6.26.0/babel.min.js"></script><!--不必要なら削除-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.26.0/polyfill.min.js"></script><!--不必要なら削除-->
    <!--自作のJS-->
    <script src="../js/4-1-2.js"></script>
</body>

</html>