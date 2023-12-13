<?php

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
    <link rel="stylesheet" media="all" href="../css/home.css" />
    <script src="../js/jquery-3.6.0.min.js"></script>
    <script src="../js/style.js"></script>
    <script src="../js/index_get.js"></script>
    <script src="../js/feedin.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../img/favicon.png">

    <!-- ローディング画面 -->
    <link rel="stylesheet" type="text/css" href="../css/4-1-2.css">
</head>
<body>

<!-- フェードイン -->
<div class="logo_fadein">
  <p><img src="../img/1.png" alt=""></p>
</div>

    <!--/splash--></div>
    <!-- headerの読み込み -->
    <?php require_once("./component/header.php")?>

    <div ><video id="top" src="../video/horse1.mp4" autoplay muted></video></div>

    <div class = 'top-mainimg'>
        <div class="mainimg">
            <h3>Let's try predicting horse racing using the uma_dansyaku!</h3>
            <p>開発者4人で力を合わせ競馬予想AIシステムを作りました!</p>
            <p>競馬初心者や競馬予想の参考が欲しい人におすすめ!</p>
        </div>
    </div>
    <main>



    </main>

    <!-- footerの読み込み -->
    <!-- <?php require_once("./component/footer.php")?> -->


    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://rawgit.com/kimmobrunfeldt/progressbar.js/master/dist/progressbar.min.js"></script>
    <!--IE11用-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/6.26.0/babel.min.js"></script><!--不必要なら削除-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.26.0/polyfill.min.js"></script><!--不必要なら削除-->
    <!--自作のJS-->
    <script src="../js/4-1-2.js"></script>
</body>

</html>