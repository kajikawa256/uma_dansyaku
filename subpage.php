<?php 
//もしrace_idが存在しなければ、index.phpに戻る
if (isset($_GET['race_id'])) {
    $race_id = $_GET['race_id'];
} else {
    header("Location: index.php"); 
}

//db.phpに接続
include('php/db.php');

try{
    //データベース接続
    $db = db_connect();

/*
    race_idを参照して、結果の馬名と馬番を表示するデータ
*/
    //race_idを使って、RNAME(馬名)を取得
    $sql_horse = "SELECT HNAME,
                         HORSENUMBER,
                         HORSEFRAME
    FROM RESULT_HORSE
    WHERE RACE_ID = :race_id
    ORDER BY RANKING
    LIMIT 5";
    $stmt = $db->prepare($sql_horse);
    $stmt->bindParam('race_id', $race_id);
    $stmt->execute();

    $result_horsename = [];
     while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){       
        $result_horsename[] = $rows;
     }
    $stmt = null;

/*
    race_idを参照して、払い戻しを詳細を表示するデータ
*/

    $sql_hit_detail = "SELECT * FROM HIT_DETAIL WHERE RACE_ID = :race_id";
    $stmt = $db->prepare($sql_hit_detail);
    $stmt->bindParam("race_id", $race_id);
    $stmt->execute();

    $result_hit_detail = [];
    while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
        $result_hit_detail[] = $rows;
    }
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
    $stmt = $db->prepare($sql_race);
    $stmt->bindParam("race_id", $race_id);
    $stmt->execute();

    $result_race_detail = [];
    while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
       $result_race_detail[] = $rows;
    }
    $stmt = null;

 /*
    race_idを参照して、レース詳細の色を識別するデータ
*/

    $sql_horsenumber_judge = "SELECT
                                HORSENUMBER,
                                HORSEFRAME
    FROM RESULT_HORSE
    WHERE RACE_ID = :race_id";
    $stmt = $db->prepare($sql_horsenumber_judge);
    $stmt->bindParam("race_id", $race_id);
    $stmt->execute();

    $result_detail_judge = [];
    while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
       $result_detail_judge[] = $rows;
    }
}catch (Exception $e){
    exit("DBエラー :" . $e->getMessage());
}finally{
    $stmt = null;
    $db = null;
}

function getBgclass($find_judge){
    $bgclass = ""; //初期クラス

    if($find_judge == 1){
        $bgclass = 'bg-white';//白
    }elseif($find_judge == 2){
        $bgclass = 'bg-black';//黒
    }elseif($find_judge == 3){
        $bgclass = 'bg-red';//赤
    }elseif($find_judge == 4){
        $bgclass = 'bg-blue';//青
    }elseif($find_judge == 5){
        $bgclass = 'bg-yellow';//黄色
    }elseif($find_judge == 6){
        $bgclass = 'bg-green';//緑
    }elseif($find_judge == 7){
        $bgclass = 'bg-orange';//橙
    }elseif($find_judge == 8){
        $bgclass = 'bg-pink';//桃
    }

    return $bgclass;
}
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
                        <h1><a href="">ウマ男爵</a></h1>
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
        <article>
            <div class="container">
                <div class="row">
                    <div class="col span-12">
                        <div class="breadcrumb">
                            <ul>
                                <li><a href="index.php">ホーム</a> > サブページ</li>
                            </ul>
                            <div class = 'race_detail'>
                                <?php foreach($result_race_detail as $race_detail) :?>
                                    <h1>
                                      <?= $race_detail['RACEDATE']?>
                                      第<?= $race_detail['RACENUMBER']?>レース
                                      <?= $race_detail['RNAME']?>
                                    </h1>
                                    <h4>
                                      <?=$race_detail['PLACE'] ?> /
                                      <?=$race_detail['TIME'] ?>発走 /
                                      天気：<?= $race_detail['WEATHER']?> /
                                      <?= $race_detail['GROUND']?><?= $race_detail['DISTANCE']?>m (<?= $race_detail['SPIN']?>) /
                                      頭数：<?= $race_detail['HORSE_TOTAL']?>頭 /
                                      馬場：<?= $race_detail['SITUATION']?> 

                                    </h4>
                                <?php endforeach ?>
                            </div>
                        </div>
                        <div class = "result"> 
                            <div class = "rank_result">
                                <h2 class = "underline">レース結果</h2>
                                <?php for($i = 0; $i < count($result_horsename); $i++) :?>
                                    <?php 
                                        $find_judge = $result_horsename[$i]["HORSEFRAME"];

                                        $bgclass = getBgclass($find_judge);

                                    ?>
                                    <h3><?= $i+1 ?>着：<span class="bg-all" id="<?=$bgclass?>"><?= $result_horsename[$i]["HORSENUMBER"] ?></span><?= $result_horsename[$i]["HNAME"]?></h3>
                                <?php  endfor ?>
                             </div>
                             <div class = "rank_result">
                                <h2 class="underline">AI予想</h2>
                                <h3>１着：１位</h3>
                                <h3>２着：２位</h3>
                                <h3>３着：３位</h3>
                                <h3>４着：４位</h3>
                                <h3>５着：５位</h3>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
        </article>
        <div class = 'top_hit_detail'>

            <div class = 'hit_detail'>
            <div id = 'null'></div>
                <div class = 'summarize_element'>
                    <h3 class = 'K kinds'>式別</h3>
                    <h3 class = 'K horsenumber'>番号</h3>
                    <h3 class = 'K betback'>払戻し</h3>
                    <h3 class = 'K popular_betback'>人気</h3>
                </div>
                
                <?php foreach($result_hit_detail as $hit_detail) : ?>
                    <div class = 'summarize_element'>
                        <h3 class = 'K kinds'><?= $hit_detail['KINDS'] ?></h3>
                        <h3 class = 'K horsenumber'> 
                        <?php 
                            if($hit_detail['KINDS'] == 'ワイド'){
                                if(preg_match('/-/',$hit_detail['HORSEFRAME'])){
                                    $horsenumbers = explode("-",$hit_detail['HORSEFRAME']);
                                    //[10, 12]

                                    $horse_total = count($horsenumbers);
                                    $horse_count = 0;

                                    foreach($horsenumbers as $horse){
                                        $find_judge = NULL;

                                       for($i=0; $i<count($result_detail_judge); $i++){
                                        if($horse == $result_detail_judge[$i]['HORSENUMBER']){
                                            $find_judge = $result_detail_judge[$i]['HORSEFRAME'];
                                        }
                                       }

                                       $bgclass = getBgclass($find_judge);

                                        echo '<span id = "' . $bgclass . '">' . $horse . '</span>';
                                        if($horse_total-1 !== $horse_count){
                                            echo ' - ';
                                        }
                                        $horse_count++;
                                    }
                                }   
                            }else{
                                if(preg_match('/-/',$hit_detail['HORSENUMBER'])){
                                    $horsenumbers = explode("-",$hit_detail['HORSENUMBER']);
                                    //[10, 12]

                                    $horse_total = count($horsenumbers);
                                    $horse_count = 0;

                                    foreach($horsenumbers as $horse){
                                        $find_judge = NULL;

                                       for($i=0; $i<count($result_detail_judge); $i++){
                                        if($horse == $result_detail_judge[$i]['HORSENUMBER']){
                                            $find_judge = $result_detail_judge[$i]['HORSEFRAME'];
                                        }
                                       }

                                       $bgclass = getBgclass($find_judge);

                                        echo '<span id = "' . $bgclass . '">' . $horse . '</span>';
                                        if($horse_total-1 !== $horse_count){
                                            echo ' - ';
                                        }
                                        $horse_count++;
                                    }
                                }elseif(preg_match('/>/',$hit_detail['HORSENUMBER'])){
                                    $horsenumbers = explode(">",$hit_detail['HORSENUMBER']);
                                    //[10, 12]

                                    $horse_total = count($horsenumbers);
                                    $horse_count = 0;

                                    foreach($horsenumbers as $horse){
                                        $find_judge = NULL;

                                       for($i=0; $i<count($result_detail_judge); $i++){
                                        if($horse == $result_detail_judge[$i]['HORSENUMBER']){
                                            $find_judge = $result_detail_judge[$i]['HORSEFRAME'];
                                        }
                                       }

                                       $bgclass = getBgclass($find_judge);

                                        echo '<span id = "' . $bgclass . '">' . $horse . '</span>';
                                        if($horse_total-1 !== $horse_count){
                                            echo ' > ';
                                        }
                                        $horse_count++;
                                    }
                                }else{
                                    $find_judge = NULL;

                                    for($i=0; $i<count($result_detail_judge); $i++){
                                        if($hit_detail['HORSENUMBER'] == $result_detail_judge[$i]['HORSENUMBER']){
                                            $find_judge = $result_detail_judge[$i]['HORSEFRAME'];
                                        }
                                    }

                                    $bgclass = getBgclass($find_judge);

                                    echo '<span id = "' . $bgclass . '">' . $hit_detail['HORSENUMBER'] . '</span>';
                                }
                            }
                            ?>                         
                        </h3>
                        <h3 class = 'K betback'><?= $hit_detail['BETBACK'] ?>円</h3>
                        <h3 class = 'K popular_betback'><?= $hit_detail['POPULAR'] ?>人気</h3>

                    </div>

                <?php endforeach ?>  

            </div>

        </div>

    </main>
    <footer>
        <div class="container">
            
            <div class="row">
                <div class="col span-4">
                    <h5>フッター１</h5>
                    <p>ここにSNSやテキストなどが入ります。SNSやテキストなどが入ります。</p>
                </div>
                <div class="col span-4">
                    <h5>フッター２</h5>
                    <p>ここにSNSやテキストなどが入ります。SNSやテキストなどが入ります。</p>
                </div>
                <div class="col span-4">
                    <h5>フッター３</h5>
                    <p>ここにSNSやテキストなどが入ります。SNSやテキストなどが入ります。</p>
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
</body>
</html>