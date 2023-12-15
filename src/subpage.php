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
} else {
    header("Location: index.php"); 
    exit();
}






/*
    race_idに基づいて、レース結果を表示
*/
    $sql_race_result = "SELECT R.HORSENUMBER,
                                R.HORSEFRAME,
                                R.HNAME,
                                R.JOCKEY,
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

    $stmt = $db->prepare($sql_race_result);
    $stmt->bindParam(":race_id", $race_id);
    $stmt->execute();
    $result_race_result = [];
    while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
        $result_race_result[] = $rows;
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

/* 
    渡された馬枠の数字によって色を変えて
    cssのidをreturnする
*/
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


/*
    オッズが10.0以下ならば文字色を変える
*/
function getOddsColor($odds){
    $bgodds = '';
    $odds = strval($odds);
    if($odds < '10.0'){
        $bgodds = 'bgodds';
    }
    return $bgodds;
}

/*
    １番人気には黄色、２番人気には青、３番人気には赤の
    背景色をつける（人気・予想着順はINT型、確定着順はString型）
*/

function getBgRankingString($ranking){
    $bgranking = ''; //初期値
    if($ranking == '1'){
        $bgranking = 'bg-ranking-yellow';
    }elseif($ranking == '2'){
        $bgranking = 'bg-ranking-blue';
    }elseif($ranking == '3'){
        $bgranking = 'bg-ranking-red';
    }
    return $bgranking;
}

function getBgRankingInt($ranking){
    $bgranking = ''; //初期値
    if($ranking == 1){
        $bgranking = 'bg-ranking-yellow';
    }elseif($ranking == 2){
        $bgranking = 'bg-ranking-blue';
    }elseif($ranking == 3){
        $bgranking = 'bg-ranking-red';
    }
    return $bgranking;
}

function getWeight($weight){
    $horse_GainLoss = '';
    if($weight > 0) {
        $horse_GainLoss = '+' . $weight;
    }elseif($weight == 0){
        $horse_GainLoss = '±' . $weight;
    }else{
        $horse_GainLoss = $weight;
    }
    return $horse_GainLoss;
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

    //馬番の色を分ける関数
    function getColorJudge($colorHorsenumber,$result_detail_judge){
        $find = '';
        foreach($result_detail_judge as $result_judge){
            if($colorHorsenumber == $result_judge['HORSENUMBER']){
                $find = $result_judge['HORSEFRAME'];
            }
        }
        $bgclass = getBgclass($find);

        return $bgclass;
    }

    /*
        「芝」or「ダート」or「障害」で背景色を切り替える関数
    */
    function getBgRaceNumber($ground){
        $bgground = '';
        if($ground == 'ダート'){
            $bgground = 'BgGroundBrown';
        }elseif($ground == '芝'){
            $bgground = 'BgGroundGreen';
        }elseif($ground == '障害'){
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
    <title>ウマ男爵</title>
    <link rel="stylesheet" media="all" href="../css/ress.min.css" />
    <link rel="stylesheet" media="all" href="../css/style.css" />
    <script src="../js/jquery-3.6.0.min.js"></script>
    <script src="../js/style.js"></script>

    <!-- Favicon -->
   <link rel="icon" type="image/png" href="../img/favicon.png"> 

</head>

<body>
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
        <article>
            <div class = 'background'>
                <div class="container_result">
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
                                                <?php
                                                $bgRaceNumber = getBgRaceNumber($race_detail['GROUND']);
                                                echo '<span id = "' . $bgRaceNumber . '">' . $race_detail["RACENUMBER"] . 'R</span>&nbsp;';
                                                echo  $race_detail['RNAME'];
                                                ?>
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
                            <?php 
                            $ua = $_SERVER['HTTP_USER_AGENT'];
                            if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false)) : ?>
                                <div class = 'result'>
                                    <table>
                                        <tr>
                                            <th class = 'r result_horsenumber c'>馬枠</th>
                                            <th class = 'r result_horsenumber c'>馬番</th>
                                            <th class = 'r result_hname c'>馬名</th>
                                            <th class = 'r result_odds c'>オッズ</th>
                                            <th class = 'r result_popular c'>人気</th>
                                            <th class = 'r result_ranking c'>AI予想</th>
                                            <th class = 'r prediction_ranking c'>確定着順</th>
                                        </tr>                                
                                        <?php foreach($result_race_result as $result_race) : ?>
                                            <tr>
                                                <td class = 'r result_horseframe'><?= $result_race['HORSEFRAME']?></td>
                                                <?php
                                                    $bgResult = getBgRankingString($result_race['RESULT_RANKING']);
                                                    $bgPopular = getBgRankingInt($result_race['POPULAR']);
                                                    $bgPrediction = getBgRankingInt($result_race['PREDICTION_RANKING']);
                                                    $bgOdds = getOddsColor($result_race['ODDS']);
                                                    $bgclass = getColorJudge($result_race['HORSENUMBER'],$result_detail_judge);
                                                    echo '<td class = "r result_horsenumber bg-all" id = "' . $bgclass . '">' . $result_race['HORSENUMBER'] . '</td>';
                                                ?>
                                                <td class = 'r result_hname'><?= $result_race['HNAME']?></td>              
                                                <?php
                                                    echo '<td class = "r result_odds" id = "' . $bgPrediction . '"><span id = "' . $bgOdds . '">' . $result_race['ODDS'] . '</span></td>';
                                                    echo '<td class = "r result_popular" id = "' . $bgPopular . '">' . $result_race['POPULAR'] . '</td>';
                                                    echo '<td class = "r result_ranking" id = "' . $bgPrediction . '">' . $result_race['PREDICTION_RANKING'] . '</td>';
                                                    echo '<td class = "r prediction_ranking" id = "' . $bgResult . '">' . $result_race['RESULT_RANKING'] . '</td>';
                                                ?>
                                            </tr>   
                                        <?php endforeach ?>
                                    </table>
                                </div>
                            <?php else : ?>
                                <div class = 'result'>
                                    <table>
                                        <tr>
                                            <th class = 'r result_horsenumber c'>馬枠</th>
                                            <th class = 'r result_horsenumber c'>馬番</th>
                                            <th class = 'r result_hname c'>馬名</th>
                                            <th class = 'r result_horse_weight c'>馬体重<br>(kg)</th>
                                            <th class = 'r result_jockey c'>騎手</th>
                                            <th class = 'r result_odds c'>オッズ</th>
                                            <th class = 'r result_popular c'>人気</th>
                                            <th class = 'r result_ranking c'>AI<br>予想</th>
                                            <th class = 'r prediction_ranking c'>確定<br>着順</th>
                                        </tr>                                
                                        <?php foreach($result_race_result as $result_race) : ?>
                                            <tr>
                                                <td class = 'r result_horseframe'><?= $result_race['HORSEFRAME']?></td>
                                                <?php
                                                    $bgResult = getBgRankingString($result_race['RESULT_RANKING']);
                                                    $bgPopular = getBgRankingInt($result_race['POPULAR']);
                                                    $bgPrediction = getBgRankingInt($result_race['PREDICTION_RANKING']);
                                                    $bgOdds = getOddsColor($result_race['ODDS']);
                                                    $horse_GainLoss = getWeight($result_race['WEIGHT_GAIN_LOSS']);
                                                    $bgclass = getColorJudge($result_race['HORSENUMBER'],$result_detail_judge);
                                                    echo '<td class = "r result_horsenumber bg-all" ><span class = "bg-all" id = "' . $bgclass . '">' . $result_race['HORSENUMBER'] . '</span></td>';
                                                ?>
                                                <td class = 'r result_hname'><?= $result_race['HNAME']?></td>              
                                                <?php echo '<td class = "r result_horse_weight">' . $result_race["HORSE_WEIGHT"] . '(' . $horse_GainLoss .  ')</td>'  ?>
                                                <td class = 'r result_jockey'><?= $result_race['JOCKEY']?></td>
                                                <?php
                                                    echo '<td class = "r result_odds" id = "' . $bgPrediction . '"><span id = "' . $bgOdds . '">' . $result_race['ODDS'] . '</span></td>';
                                                    echo '<td class = "r result_popular" id = "' . $bgPopular . '">' . $result_race['POPULAR'] . '人気</td>';
                                                    echo '<td class = "r result_ranking" id = "' . $bgPrediction . '">' . $result_race['PREDICTION_RANKING'] . '着</td>';
                                                    if(strcmp($result_race['RESULT_RANKING'],'失格') == 0 or strcmp($result_race['RESULT_RANKING'],'取消') == 0 or strcmp($result_race['RESULT_RANKING'],'除外') == 0 or strcmp($result_race['RESULT_RANKING'],'中止') == 0){
                                                        echo '<td class = "r prediction_ranking" id = "' . $bgResult . '">' . $result_race['RESULT_RANKING'] . '</td>';
                                                    }else{
                                                        echo '<td class = "r prediction_ranking" id = "' . $bgResult . '">' . $result_race['RESULT_RANKING'] . '着</td>';
                                                    }
                                                  
                                                ?>
                                            </tr>   
                                        <?php endforeach ?>
                                    </table>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </article>
            <div class = "container_summarize">
                <div class = "row">
                    <div class = "col span-12">
                        <div class = 'top_hit_detail'>
                            <div class = 'hit_detail'>
                                <div class = 'summarize_element'>
                                
                                <table>
                                    <tr>
                                        <th class = 'K kinds C'>式別</th>
                                        <th class = 'K horsenumber horsenumber_title C'>番号</th>
                                        <th class = 'K betback C'>払戻し</th>
                                        <th class = 'K popular_betback C'>人気</th><br>  
                                    </tr>
                                    <?php foreach($result_hit_detail as $hit_detail) : ?>   
                                    <tr>
                                        <td class = 'K kinds'><?= $hit_detail['KINDS'] ?></td>
                                        <?php 
                                            if($hit_detail['KINDS'] == 'ワイド'){
                                                if(preg_match('/-/',$hit_detail['HORSEFRAME'])){
                                                    $horsenumbers = explode("-",$hit_detail['HORSEFRAME']);
                                                    //[1 , 3]
                                                    // var_dump($horsenumbers);

                                                    $horse_total = count($horsenumbers);
                                                    $horse_count = 0;

                                                    for($i=0; $i<=$horse_total; $i++){
                                                        $find_judge = NULL;

                                                        if($i !== $horse_total){
                                                            for($j=0; $j<count($result_detail_judge); $j++){
                                                                if($horsenumbers[$i] == $result_detail_judge[$j]['HORSENUMBER']){
                                                                    $find_judge = $result_detail_judge[$j]['HORSEFRAME'];
                                                                }
                                                            }   
                                                        }

                                                        $bgclass = getBgclass($find_judge);

                                                        // echo 'hhhhhh';
                                                        if($horse_count == 0){
                                                            echo '<td class = "bg-all K horsenumber"><span id = "' . $bgclass . '">' . $horsenumbers[$i] . '</span>'; 
                                                           
                                                        }else if($horse_total !== $horse_count){
                                                            echo '<span class = "num"> - </span> <span id = "' . $bgclass . '">' . $horsenumbers[$i] . '</span>';
                                                           
                                                        }else{
                                                            echo '</td>';
                                                           
                                                        }
                                                        $horse_count++;
                                                    }
                                                }   
                                            }else{
                                                if(preg_match('/-/',$hit_detail['HORSENUMBER'])){
                                                    $horsenumbers = explode("-",$hit_detail['HORSENUMBER']);
                                                    //[1 , 3]
                                                    // var_dump($horsenumbers);

                                                    $horse_total = count($horsenumbers);
                                                    $horse_count = 0;

                                                    for($i=0; $i<=$horse_total; $i++){
                                                        $find_judge = NULL;

                                                        if($i !== $horse_total){
                                                            for($j=0; $j<count($result_detail_judge); $j++){
                                                                if($horsenumbers[$i] == $result_detail_judge[$j]['HORSENUMBER']){
                                                                    $find_judge = $result_detail_judge[$j]['HORSEFRAME'];
                                                                }
                                                            }
                                                        }

                                                    $bgclass = getBgclass($find_judge);


                                                  
                                                    if($horse_count == 0){
                                                        echo '<td class = "bg-all K horsenumber"><span id = "' . $bgclass . '">' . $horsenumbers[$i] . '</span>';                                               
                                                    }else if($horse_total !== $horse_count){
                                                        echo '<span class = "num"> - </span> <span id = "' . $bgclass . '">' . $horsenumbers[$i] . '</span>';                                                 
                                                    }else{
                                                        echo '</td>';                                   
                                                    }
                                                    $horse_count++;
                                                }
                                            }elseif(preg_match('/>/',$hit_detail['HORSENUMBER'])){
                                                $horsenumbers = explode(">",$hit_detail['HORSENUMBER']);
                                                //[1 , 3]
                                                // var_dump($horsenumbers);

                                                $horse_total = count($horsenumbers);
                                                $horse_count = 0;

                                                for($i=0; $i<=$horse_total; $i++){
                                                    $find_judge = NULL;

                                                    if($i !== $horse_total){
                                                        for($j=0; $j<count($result_detail_judge); $j++){
                                                            if($horsenumbers[$i] == $result_detail_judge[$j]['HORSENUMBER']){
                                                                $find_judge = $result_detail_judge[$j]['HORSEFRAME'];
                                                            }
                                                        }
                                                    }

                                                    $bgclass = getBgclass($find_judge);

                                                    if($horse_count == 0){
                                                        echo '<td class = "bg-all K horsenumber"><span id = "' . $bgclass . '">' . $horsenumbers[$i] . '</span>';
                                                    }else if($horse_total !== $horse_count){
                                                        echo '<span class = "num"> > </span> <span id = "' . $bgclass . '">' . $horsenumbers[$i] . '</span>';
                                                    }else{
                                                        echo '</td>';
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
                                                echo '<td class = "bg-all K horsenumber"><span id = "' . $bgclass . '">' . $hit_detail['HORSENUMBER'] . '</span></td>';
                                            }
                                            }
                                        ?>                         
                                        <td class = 'K betback'><?= $hit_detail['BETBACK'] ?>円</td>
                                        <td class = 'K popular_betback'><?= $hit_detail['POPULAR'] ?>人気</td>
                                    </tr>
                                  
                                <?php endforeach ?>
                              
                                </table>
                               
                            </div>
                        </div>
                    </div>
                </div>
                <img src="../img/umamusume.jpg" alt="準備中" width = 300px height = 200px margin-left = 20px>
            </div>
            
        </div>
    </main>
    <!-- <footer>
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
    </footer> -->
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