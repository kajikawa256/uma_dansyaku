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

    //race_idを使って、RNAME(馬名)を取得
    $sql = "SELECT HNAME,HORSENUMBER FROM RESULT_HORSE WHERE RACE_ID = :race_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam('race_id', $race_id);
    $stmt->execute();
    $result_horsename = [];
     while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
        $result_horsename[] = $rows;
     }
}catch (Exception $e){
    exit("DBエラー :" . $e->getMessage());
}finally{
    $stmt = null;
    $db = null;
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
                        <h1><a href="index.html">ウマ男爵</a></h1>
                        <div class="snsbox">
                            <a href="https://instagram.com/shousei._.xx?igshid=NGVhN2U2NjQ0Yg%3D%3D&utm_source=qr"><img src="img/in-icon.png" alt="Instagram"></a>
                            <a><img src="img/fb-icon.png" alt="Facebook"></a>
                        </div>
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
                                <li><a href="index.html">ホーム</a></li>
                                <li><a href="subpage.html">競馬予想</a></li> 
                                <li><a href="subpage.html">お問い合わせ</a></li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <div class="mainimg">
        <img src="img/uma_main.jpg" alt="サブページ画像">
    </div>
    <main>
        <article>
            <div class="container">
                <div class="row">
                    <div class="col span-12">
                        <div class="breadcrumb">
                            <ul>
                                <li><a href="index.html">ホーム</a> > サブページ</li>
                            </ul>
                        </div>
                        <div class = "result"> 
                            <div class = "rank_result">
                                <h2 class = "underline">予想</h2>
                                <?php for($i = 0; $i < count($result_horsename); $i++) :?>
                                    <h3><?= $i+1 ?>着：<?= $result_horsename[$i]["HORSENUMBER"] ?> <?= $result_horsename[$i]["HNAME"]?></h3>
                                <?php  endfor ?>
                             </div>
                             <div class = "rank_result">
                                <h2 class="underline">１０月７日の予想結果</h2>
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