<?php 
//db.phpに接続
include('../php/db.php');

try{
    //データベース接続
    $db = db_connect();
//もしrace_idが存在しなければ、index.phpに戻る
if (isset($_GET['race_id'])) {
    
    $race_id = $_GET['race_id'];

    //文字数制限
    $Length = 12;

    //文字列の確認
    if(mb_strlen($race_id) !== $Length){
        echo "あああ";
        header("Location: index.php");
    }
    
    // 数字以外の文字を排除
    if (!ctype_digit($race_id)) {
        header("Location: index.php");
        exit();
    }
    

    //データベースの存在チェック
    $stmt = $db->prepare("SELECT COUNT(*)AS COUNT FROM RACE WHERE race_id = :race_id");
    $stmt->bindParam(':race_id', $race_id, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    var_dump($result['count']);

    // //プリペアドステートメントを閉じる
    // $stmt = null;

    // if($result['count'] == 0){
    //     echo "あああ";
    //     header("Location: index.php");
    //     exit();
    // }
} else {
    echo "あああ";
    header("Location: index.php"); 
    
    exit();
}






/*
    race_idを参照して、結果の馬名と馬番を表示するデータ
*/
    //race_idを使って、RNAME(馬名)を取得
    $sql_horse = "SELECT HNAME,
                         HORSENUMBER,
                         HORSEFRAME,
                         RANKING
    FROM RESULT_HORSE
    WHERE RACE_ID = :race_id
    LIMIT 5";
    $stmt = $db->prepare($sql_horse);
    $stmt->bindParam(':race_id', $race_id);
    $stmt->execute();

    $result_horsename = [];
     while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){       
        $result_horsename[] = $rows;
     }
    $stmt = null;

/*
    race_idを参照して、予想結果の馬名と馬番を表示するデータ
*/
    //race_idを使って、RNAME(馬名)を取得
    $sql_horse_prediction = "SELECT HNAME,
                         HORSENUMBER,
                         HORSEFRAME,
                         RANKING
    FROM PREDICTION_HORSE
    WHERE RACE_ID = :race_id
    LIMIT 5";
    $stmt = $db->prepare($sql_horse_prediction);
    $stmt->bindParam(':race_id', $race_id);
    $stmt->execute();

    $prediction_horsename = [];
     while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){       
        $prediction_horsename[] = $rows;
     }
    $stmt = null;

/*
    race_idを参照して、払い戻しを詳細を表示するデータ
*/

    $sql_hit_detail = "SELECT * FROM HIT_DETAIL WHERE RACE_ID = :race_id";
    $stmt = $db->prepare($sql_hit_detail);
    $stmt->bindParam(":race_id", $race_id);
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
    $stmt->bindParam(":race_id", $race_id);
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
    $stmt->bindParam(":race_id", $race_id);
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

/* 馬番の色分け関数 */
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

/* 天気マーク関数 */
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

    <!-- Favicon -->
   <link rel="icon" type="image/png" href="../img/favicon.png"> 

</head>

<body>
    <header>
        <div class="container">
            <div class="row">
                <div class="col span-12">
                    <div class="head">
                        <h1><a href="index.php">ウマ男爵</a></h1>
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
            <div class = 'background'>
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
                                        <?= $race_detail['RACEDATE']?>のAI予想結果
                                        </h1>
                                        <div class = 'race_infomation'>
                                            <h3>
                                            <?= $race_detail['RACENUMBER']?>R
                                            <?= $race_detail['RNAME']?>
                                            </h3>
                                            <p>
                                            <?=$race_detail['PLACE'] ?> /
                                            <?=$race_detail['TIME'] ?>発走 /
                                            天気：<?php        
                                                      $weather = $race_detail['WEATHER'];
                                                      getWeather($weather);
                                                    ?> /
                                            <?= $race_detail['GROUND']?><?= $race_detail['DISTANCE']?>m (<?= $race_detail['SPIN']?>) /
                                            頭数：<?= $race_detail['HORSE_TOTAL']?>頭 /
                                            馬場：<?= $race_detail['SITUATION']?> 

                                            </p>
                                        </div>
                                        
                                    <?php endforeach ?>
                                </div>
                            </div>
                            <div class = "result"> 
                                <div class = "rank_result_result">
                                    <p class = "underline">レース結果</p>
                                    <?php for($i = 0; $i < count($result_horsename); $i++) :?>
                                        <?php 
                                            $find_judge = $result_horsename[$i]["HORSEFRAME"];

                                            $bgclass = getBgclass($find_judge);

                                        ?>
                                        <p><?= $result_horsename[$i]['RANKING'] ?>着：<span class="bg-all" id="<?=$bgclass?>"><?= $result_horsename[$i]["HORSENUMBER"] ?></span>  <?= $result_horsename[$i]["HNAME"]?></p>
                                    <?php  endfor ?>
                                </div>
                                <div class = "rank_result_prediction">
                                    <p class="underline">AI予想結果</p>
                                    <?php for($i = 0; $i < count($prediction_horsename); $i++) :?>
                                        <?php 
                                            $find_judge = $prediction_horsename[$i]["HORSEFRAME"];

                                            $bgclass = getBgclass($find_judge);

                                        ?>
                                        <p><?= $prediction_horsename[$i]['RANKING'] ?>着：<span class="bg-all" id="<?=$bgclass?>"><?= $prediction_horsename[$i]["HORSENUMBER"] ?></span>  <?= $prediction_horsename[$i]["HNAME"]?></p>
                                    <?php  endfor ?>
                                </div>  
                            </div>
                        </div>
                    </div>
                </div>
            </article>
            <div class = "container">
                <div class = "row">
                    <div class = "col span-12">
                        <div class = 'top_hit_detail'>
                            <div class = 'hit_detail'>
                            <div id = 'null'></div>
                                <div class = 'summarize_element'>
                                    <p class = 'K kinds C'>式別</p>
                                    <p class = 'K horsenumber horsenumber_title C'>番号</p>
                                    <p class = 'K betback C'>払戻し</p>
                                    <p class = 'K popular_betback C'>人気</p>
                                </div>
                                
                                <?php foreach($result_hit_detail as $hit_detail) : ?>
                                    <div class = 'summarize_element'>
                                        <p class = 'K kinds'><?= $hit_detail['KINDS'] ?></p>
                                        <p class = 'K horsenumber'> 
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

                                                        echo '<span class = "bg-all" id = "' . $bgclass . '">' . $horse . '</span>';
                                                        if($horse_total-1 !== $horse_count){
                                                            echo '<span class = "num"> - </span>';
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

                                                        echo '<span class = "bg-all" id = "' . $bgclass . '">' . $horse . '</span>';
                                                        if($horse_total-1 !== $horse_count){
                                                            echo '<span class = "num"> - </span>';
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

                                                        echo '<span class = "bg-all" id = "' . $bgclass . '">' . $horse . '</span>';
                                                        if($horse_total-1 !== $horse_count){
                                                            echo '<span class = "num"> > </span>';
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

                                                    echo '<span class = "bg-all" id = "' . $bgclass . '">' . $hit_detail['HORSENUMBER'] . '</span>';
                                                }
                                            }
                                            ?>                         
                                        </p>
                                        <p class = 'K betback'><?= $hit_detail['BETBACK'] ?>円</p>
                                        <p class = 'K popular_betback'><?= $hit_detail['POPULAR'] ?>人気</p>
                                    </div>
                                <?php endforeach ?>  
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </main>
    <footer>
        <div class="container">
            
            <div class="row">
                <div class="col span-4">
                  
                </div>
                <div class="col span-4">
                    
                </div>
                <div class="col span-4">
        
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
    <!--自作のJS-->
    <script src="../js/4-1-2.js"></script>
</body>
</html>