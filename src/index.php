<?php
include('../php/index_call.php');
$hit = ROUND($result_hitcount[0]["hitcount"] / $result_racecount[0]["racecount"] * 100,1);
$race_money = $result_racecount[0]["racecount"] * 100;
$collect = ROUND(intval($result_collect[0]['collect']) / $race_money * 100,1);
$count = 0;
$flag = true;

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

    <header>
        <div class="container">
            <div class="row">   
                <div class="col span-12">
                    <div class="head">
                        <h1><a href="index.php"><img src="../img/1.png" alt="準備中" width="150px" height="150px"> </a></h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col span-12">
                    <nav>
                        <div id="open"></div>
                        <div id="close"></div>
                        <div id="navi">
                            <ul>
                                <li><a href="index.php">ホーム</a></li>
                                <li><a href="subpage.html">競馬予想</a></li> 
                                <li><a href="subpage.html">お問い合わせ</a></li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <div class = 'top-mainimg'>
        <div class="mainimg">
            <h3>Let's try predicting horse racing using the uma_dansyaku!</h3>
            <p>開発者4人で力を合わせ競馬予想AIシステムを作りました!</p>
            <p>競馬初心者や競馬予想の参考が欲しい人におすすめ!</p>
        </div>
    </div>
    <main>
        <section>
            <div class="container">
                <h2 id=race class="catch">～競馬予想～</h2>
                


                <div class = 'race_head'>
                    <form action="index.php" id = "myform" method = "GET">
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

                    </form>
                </div>
                                    
            </div>
            <div id = "race_data">
                <?php for($i = 0; $i < count($result_race) / 3; $i++):?>
                    
                    <div class="row">
                        <?php for($j = 0; $j < 3; $j++) :?> <!-- 3は横並びにする数 -->
                        <?php if($count == count($result_race)){break;} ?>
                            
                            <!-- 場所を指定して絞り込む -->
                            <?php if(strcmp($result_race[$count]["PLACE"],$_GET["raceplace"]) == 0) : ?>
                            <div class="col span-4">
                            <a href="subpage.php?race_id=<?= $result_race[$count]["RACE_ID"] ?>">
                                    <h5>第<?= $result_race[$count]["RACENUMBER"]?>レース  <?= $result_race[$count]["PLACE"] ?><br><?= $result_race[$count]["RNAME"]?></h5>
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
                                    <h5>第<?= $result_race[$count]["RACENUMBER"]?>レース  <?= $result_race[$count]["PLACE"] ?><br><?= $result_race[$count]["RNAME"]?></h5>
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
                                    <h5>第<?= $result_race[$count]["RACENUMBER"]?>レース  <?= $result_race[$count]["PLACE"] ?><br><?= $result_race[$count]["RNAME"]?></h5>
                                    <p><?= $result_race[$count]["TIME"] ?>発走  <?= $result_race[$count]["GROUND"] ?> <?= $result_race[$count]["DISTANCE"] ?>m  天気：
                                    <?php 
                                     $weather = $result_race[$count]["WEATHER"];
                                     getWeather($weather);
                                    ?></p>
                                </a>
                            </div>                     
                            <?php endif; $count++;?>

                        <?php endfor ?>           
                    </div>

                <?php endfor; ?>   
            </div>
        </section>
    </main>
    <footer>
        <div class="container">
            <div class="row">
                <div class="col span-13">
                    <h5>参考サイト</h5>
                    <p><a href="https://race.netkeiba.com/race/shutuba.html?race_id=202309010101">netkeiba</a></p>
                </div>
                <div class="col span-13">
                    <h5>10月7日の回収率</h5>
                    <p><?= $collect?>%</p>
                </div>
                <div class="col span-13">
                    <h5>10月7日の的中率</h5>
                    <p><?= $hit?>%</p>
                </div>
            </div>
        </div>
    </footer>
    <div class="copyright">
        <div class="container">
            <div class="row">
                <div class="col">
                    <hr>
                    Copyright © <a href="https://popo-design.net" target="_blank">popodesign. </a>

                </div>
            </div>
        </div>
    </div>
    <p id="pagetop"><a href="#">TOP</a></p>


    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://rawgit.com/kimmobrunfeldt/progressbar.js/master/dist/progressbar.min.js"></script>
    <!--IE11用-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/6.26.0/babel.min.js"></script><!--不必要なら削除-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.26.0/polyfill.min.js"></script><!--不必要なら削除-->
    <!--自作のJS-->
    <script src="../js/4-1-2.js"></script>
</body>

</html>