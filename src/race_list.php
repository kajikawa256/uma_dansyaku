<?php
include('../php/index_call.php');
$hit = ROUND($result_hitcount[0]["hitcount"] / $result_racecount[0]["racecount"] * 100,1);
$race_money = $result_racecount[0]["racecount"] * 100;
$collect = ROUND(intval($result_collect[0]['collect']) / $race_money * 100,1);
$count = 0;
$racecount = 0;
$flag = true;

/*
    「RNAME」の文字数を削減
*/

function truncateString($inputString, $maxLength = 10) {
    if (mb_strlen($inputString) > $maxLength) {
        $truncatedString = mb_substr($inputString, 0, $maxLength - 1) . '...';
        return $truncatedString;
    } else {
        return $inputString;
    }
}



if(strcmp($_GET["racedate"],"")){
    $position = 680;
}else{
    $position = 0;
}

function getWeather($weather){
   $icon = '';
   switch($weather){
    case '晴' :
        $icon = 'tennki-illust1.png';
        break;
    case '曇' :
        $icon = 'tennki-illust5.png';
        break;
    case '雨' :
        $icon = 'tennki-illust7.png';
        break;
    case '小雨' :
        $icon = 'tennki-illust17.png';
        break;
    default :
        break;
   }

if($icon !== ''){
    echo ('<img class="weather_icon" src="../img/' . $icon . '" alt="準備中" width="30px" height="30px">');
}

}

?>
<!-- ページの自動スクロール -->
<script>
    window.onload = function (){	$(window).scrollTop(<?php echo $position; ?>);}
</script>

<!doctype html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ウマ男爵</title>
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
    <!--/splash--></div>

    <!-- headerの読み込み -->
    <?php require_once("./component/header.php")?>

    <div class = 'top-mainimg'>
        <div class="mainimg">
            <h3>Let's try predicting horse racing using the uma_dansyaku!</h3>
            <p>開発者4人で力を合わせ競馬予想AIシステムを作りました!</p>
            <p>競馬初心者や競馬予想の参考が欲しい人におすすめ!</p>
        </div>
    </div>
    <main>
        <section id ="main">
            <div class="container" id = ''>
                <div class = 'misosiru'>
                    <?php
                            $ua = $_SERVER['HTTP_USER_AGENT'];
                            if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false)) {$msg = "";}else{$msg = "（全10開催場）";}?>
                    <h2 id=race class="catch">中央競馬レース予想<?= $msg;?></h2>
                    <div class = 'race_head'>
                        <form action="race_list.php" id = "myform" method = "GET">
                            <div id = "select_tab">
                            <label class="selectbox-001">
                                <select id = 'pulldown_racedate' name = 'racedate' onchange = "submit(this.form)" onclick="buttonClick()" >
                                    <?php foreach($result_race_date as $x) :?>

                                        <!-- 日付のプルダウンを表示 選択した日付をデフォルトとして表示 -->
                                        <?php if(strcmp($x["RACEDATE"],$_GET["racedate"])) {
                                            echo("<option value = ". $x["RACEDATE"] .">". $x["RACEDATE"] . "</option>");
                                        } else {
                                            echo("<option value ='". $x["RACEDATE"] ."'selected>". $x["RACEDATE"] . "</option>");
                                        }
                                        ?>
                                    <?php endforeach ?>
                                </select>
                            </label>
                            </div>

                            <div id = "select_tab">
                                <label class="selectbox-001">
                                    <select id = 'pulldown_raceplace' name = 'raceplace' onchange = "submit(this.form)" >
                                        <?php foreach($result_race_place as $x) :?>
                                            <!-- 開催場所のプルダウンを表示 選択した開催場所をデフォルトとして表示 -->
                                            <?php if(strcmp($x["PLACE"],$_GET["raceplace"])) {
                                                echo("<option value = ". $x["PLACE"] .">". $x["PLACE"] . "</option>");
                                            } else {
                                                echo("<option value ='". $x["PLACE"] ."'selected>". $x["PLACE"] . "</option>");
                                            }
                                            ?>
                                        <?php endforeach ?>
                                    </select>
                                </label>
                            </div>
                            <!-- <div class = 'search-form-005'>
                            <label>
                                <input type="text" placeholder="キーワードを入力">
                            </label>
                            <button type="submit" aria-label="検索"></button>
                            </div>         -->
                        </form>
                    </div>
                </div>
            </div>
            <div id = "contents">
            <div id = "race_data">
                <?php for($i = 0; $i < count($result_race) / 3; $i++):?>

                    <div class="row">
                        <?php for($j = 0; $j < 3; $j++) :?> <!-- 3は横並びにする数 -->
                        <?php if($count == count($result_race)){break;} ?>

                            <!-- 場所を指定して絞り込む -->
                            <?php if(strcmp($result_race[$count]["PLACE"],$_GET["raceplace"]) == 0) : ?>
                            <div class="col span-4">
                            <a href="subpage.php?race_id=<?= $result_race[$count]["RACE_ID"] ?>">
                                <?php 
                                    $output = truncateString($result_race[$count]["RNAME"], 9);
                                ?>
                                    <h5 class="race_title"><?= $result_race[$count]["RACENUMBER"]?>R <?= $output?>
                                    <?php 
                                        if($result_hitcheck[$racecount]['RESULT_NAME'] == $result_hitcheck[$racecount]['PREDICTION_NAME']){
                                            echo '<div id = "hit_icon_top"><img class="hit_icon" src="../img/的中.png" alt="準備中"></div>';
                                        }
                                    ?>
                                    </h5>
                                    <p><?= $result_race[$count]["TIME"] ?>発走  <?= $result_race[$count]["GROUND"] ?> <?= $result_race[$count]["DISTANCE"] ?>m  天気 : 
                                    <?php 
                                        $weather = $result_race[$count]["WEATHER"];
                                        getWeather($weather);
                                    ?></p>
                                </a>
                            </div> 
                            <?php $flag = false; ?>
                            <?php  endif;?>

                            <!-- 初回接続時 -->
                            <?php if(strcmp($_GET["raceplace"],"") == 0): ?>
                                <div class="col span-4">
                                <a href="subpage.php?race_id=<?= $result_race[$count]["RACE_ID"] ?>">
                                    <?php 
                                        $output = truncateString($result_race[$count]["RNAME"], 9);
                                    ?>
                                    <h5 class="race_title"><?= $result_race[$count]["RACENUMBER"]?>R  <?= $output?>
                                    <?php 
                                        if($result_hitcheck[$racecount]['RESULT_NAME'] == $result_hitcheck[$racecount]['PREDICTION_NAME']){
                                            echo '<div id = "hit_icon_top"><img class="hit_icon" src="../img/的中.png" alt="準備中"></div>';
                                        }
                                    ?>
                                    </h5>
                                    <p><?= $result_race[$count]["TIME"] ?>発走  <?= $result_race[$count]["GROUND"] ?> <?= $result_race[$count]["DISTANCE"] ?>m  天気：
                                    <?php 
                                       $weather = $result_race[$count]["WEATHER"];
                                       getWeather($weather);
                                    ?></p>
                                </a>
                            </div> 
                            <?php $flag = false; ?>
                            <?php endif;?>
                            
                            <!-- どれにも該当しなかった場合 -->
                            <?php if($flag && $count < 12): ?>
                                <div class="col span-4">
                                <a href="subpage.php?race_id=<?= $result_race[$count]["RACE_ID"] ?>">
                                    <?php 
                                        $output = truncateString($result_race[$count]["RNAME"], 9);
                                    ?>
                                    <h5 class="race_title"><?= $result_race[$count]["RACENUMBER"]?>R <?= $output?>
                                    <?php 
                                        if($result_hitcheck[$racecount]['RESULT_NAME'] == $result_hitcheck[$racecount]['PREDICTION_NAME']){
                                            echo '<div id = "hit_icon_top"><img class="hit_icon" src="../img/的中.png" alt="準備中"></div>';
                                        }
                                    ?>
                                    </h5>
                                    <p><?= $result_race[$count]["TIME"] ?>発走  <?= $result_race[$count]["GROUND"] ?> <?= $result_race[$count]["DISTANCE"] ?>m  天気：
                                    <?php 
                                     $weather = $result_race[$count]["WEATHER"];
                                     getWeather($weather);
                                    ?></p>
                                </a>
                            </div>                     
                            <?php endif; $count++;$racecount++;?>

                        <?php endfor?>           
                    </div>
                <?php endfor; ?>   
            </div>
            <div id = "side_var">
                <div class="side_centense">
                    <h4>タイトル</h4>
                    <p>最新情報、人気記事、特集コンテンツ。カテゴリー別に検索してみてください。新着アップデートやお得な情報も随時更新中。質問やご意見はお気軽にお知らせください。</p>
                </div>
                <div class="side_centense">
                    <h4>人気馬ランキング</h4>
                    <p>1位　イクイノックス</p>
                    <p>2位　キタサンブラック</p>
                    <p>3位　ジャスティンパレス</p>
                </div>
                <img src="../img/side.jpg" class="koukoku">
                <img src="../img/side2.jpg" class="koukoku">
                <img src="../img/side3.jpg" class="koukoku">
            <div>
            </div>
        </section>
    </main>

    <!-- footerの読み込み -->
    <?php require_once("./component/footer.php") ?>


    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://rawgit.com/kimmobrunfeldt/progressbar.js/master/dist/progressbar.min.js"></script>
    <!--IE11用-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/6.26.0/babel.min.js"></script><!--不必要なら削除-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.26.0/polyfill.min.js"></script><!--不必要なら削除-->
    <!--自作のJS-->
    <script src="../js/4-1-2.js"></script>
</body>

</html>