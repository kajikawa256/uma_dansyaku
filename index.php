<?php
include('php/index_call.php');
$count = 0;


?>

<!doctype html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ウマ男爵</title>
    <link rel="stylesheet" media="all" href="css/ress.min.css" />
    <link rel="stylesheet" media="all" href="css/style.css" />
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/style.js"></script>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/favicon.png">

</head>
<body>
    <header>
        <div class="container">
            <div class="row">   
                <div class="col span-12">
                    <div class="head">
                        <h1><a href="index.html">ウマ男爵 </a></h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col span-12">
                    <nav>
                        <div id="open"></div>
                        <div id="close"></div>
                        <div id="navi">
                            <!-- <ul>
                                <li><a href="index.html">ホーム</a></li>
                                <li><a href="subpage.html">競馬予想</a></li> 
                                <li><a href="subpage.html">お問い合わせ</a></li>
                            </ul> -->
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
                    <form action="index.php#race" id = "myform" method = "GET">
                        <h3>
                            <select id = 'pulldown_racedate' name = 'select_racedate'>
                                <?php foreach($result_race_date as $x) :?>
                                    <option value =<?= $x["RACEDATE"] ?> ><?= $x["RACEDATE"] ?></option>
                                <?php endforeach ?>  開催場：
                            </select>
                            <button type="submit">送信</button>
                            <?php for ($i = 0; $i < count($result_race_place); $i++) : ?> 
                                <a href="index.php?place=<?= $result_race_place[$i]["PLACE"] ?>"><?= $result_race_place[$i]["PLACE"] ?></a>
                            <?php endfor ?>
                        </h3>
                    </form>
                </div>
                <?php for($i = 0; $i < count($result_race) / 3; $i++):?>
                    <div class="row">
                        <?php for($j = 0; $j < 3; $j++) :?> <!-- 3は横並びにする数 -->
                            <div class="col span-4">
                            <a href="subpage.php?race_id=<?= $result_race[$count]["RACE_ID"] ?>">
                                    <h5>第<?= $result_race[$count]["RACENUMBER"]?>レース<br><?= $result_race[$count]["RNAME"]?></h5>
                                    <p><?= $result_race[$count]["TIME"] ?>発走  <?= $result_race[$count]["GROUND"] ?> <?= $result_race[$count]["DISTANCE"] ?>m  天気：<?= $result_race[$count]["WEATHER"] ?></p>
                                </a>
                            </div> 
                            <?php $count++; ?> 
                        <?php endfor ?>           
                    </div>
                <?php endfor ?>   
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
                    <h6><?= $result_hit[0]["COLLECT_PR"] ?>%</h6>
                </div>
                <div class="col span-13">
                    <h5>10月7日の的中率</h5>
                    <h6><?= $result_hit[0]["HIT_PR"]?>%</h6>
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
    <script src="js/index_get.js"></script>
</body>

</html>