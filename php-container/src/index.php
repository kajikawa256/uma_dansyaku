<?php
# セッション変数とスマホかPCかチェックする変数の定義
session_start();
$ua = $_SERVER['HTTP_USER_AGENT'];
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
    <title>ウマ男爵 - ホームページ</title>
    <link rel="stylesheet" media="all" href="./css/ress.min.css" />
    <link rel="stylesheet" media="all" href="./css/style.css" />
    <link rel="stylesheet" media="all" href="./css/home.css" />
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/style.js"></script>
    <script src="./js/index_get.js"></script>
    <script src="./js/feedin.js"></script>
    <script src="./js/text.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="./img/favicon.png">

    <!-- ローディング画面 -->
    <link rel="stylesheet" type="text/css" href="./css/4-1-2.css">

</head>


<body>

    <!-- 初回接続時のみフェードイン -->
    <?php if (!isset($_SESSION["visited"])):
        $_SESSION["visited"] = 1; ?>
        <div class="logo_fadein">
            <p><img src="./img/1.png" alt=""></p>
        </div>
        <link rel="stylesheet" media="all" href="./css/lock_schrool.css" />
    <?php endif; ?>


    <!-- headerの読み込み -->
    <?php require_once("./component/header.php") ?>


    <main>
        <!-- 馬の動画とタイトル -->
        <div id="main_top_movie">
            <!-- スマホなら画像、PCなら動画を表示 -->
            <?php if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false)): ?>
                <img id="top" src="./img/top_main.jpg">
            <?php else: ?>
                <video id="top" src="./video/horse1.mp4" autoplay muted playsinline loop></video>
            <?php endif; ?>
            <div>
                <img id="title_log" src="./img/logo_2.png">
            </div>
        </div>

        <!-- Descryption -->
        <div class="contents">
            <h3 class="cp_h3title">Descryption</h3>
            <div class="box-design6">

                <?php if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false)): ?>
                    <p>「ウマ男爵」は競馬初心者の方から玄人の方まで幅広いユーザを対象としたWebサイトです。</p>
                    <p>競馬で遊んでみたいという方や、最近なかなか勝てなくて...といった方にお勧めのサイトです。</p>
                <?php else: ?>
                    <div class="fadein fadein-left blue">
                        <p>当サイト「ウマ男爵」は競馬初心者の方から玄人の方まで幅広いユーザを対象としたWebサイトです。</p>
                        <p>競馬で遊んでみたいという方や、最近なかなか勝てなくて...といった方にお勧めのサイトです。</p>
                        <p>私たちは"回収率100%越え"を目標に、AIの開発、サイトのアップデートを日々行っています。</p>
                    </div>
                <?php endif; ?>

                <div id="next_button">
                    <a href="race_list.php" class="btn_01">予想を見る ></a>
                </div>
            </div>

        </div>
        </div>


        <!-- Technology -->
        <div class="contents">
            <h3 class="cp_h3title">Technology</h3>
            <div id="technology">
                <img src="./img/deep.png">
                <div id="explain" class="wrap">
                    <p class="typing">
                        ディープラーニングは、人間の神経回路を模倣した仕組み。多層のニューラルネットが複雑な非線形パターンを学習し、活性化関数と誤差逆伝播法で重みを調整しています。この構造は生物学的な神経系統に類似し、高度な情報処理を可能にしています。
                    </p>
                </div>
            </div>
        </div>


        <!-- Diary -->
        <?php if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false)): ?>
        <?php else: ?>
            <div class="contents">
                <h3 class="cp_h3title">History</h3>
                <div class="cp_timeline03">
                    <div class="timeline_group">
                        <span class="time_year">2023</span>
                        <div class="timeline_item">
                            <div class="time">
                                <span class="time_day">1</span>
                                <span class="time_month">Oct</span>
                            </div>
                            <div class="desc">
                                <p class="flag">企画</p>
                                近年注目されているAIにフォーカスを当て、競馬予想AIサイトの企画を発案
                            </div>
                        </div>
                        <div class="timeline_item">
                            <div class="time">
                                <span class="time_day">10</span>
                                <span class="time_month">Oct</span>
                            </div>
                            <div class="desc">
                                <p class="flag">要件定義</p>
                                競馬予想AIの予想を公開するにあたって、必要な機能等の洗い出し
                            </div>
                        </div>
                        <div class="timeline_item">
                            <div class="time">
                                <span class="time_day">30</span>
                                <span class="time_month">Oct</span>
                            </div>
                            <div class="desc">
                                <p class="flag">開発開始</p>
                                制作メンバーを結成し、開発作業に取り組む
                            </div>
                        </div>
                    </div>
                    <div class="timeline_group">
                        <span class="time_year">20xx</span>
                        <div class="timeline_item">
                            <div class="time">
                                <span class="time_day">12</span>
                                <span class="time_month">Nov</span>
                            </div>
                            <div class="desc">
                                <p class="flag">初代モデル完成</p>
                                第1モデルが完成。的中率は30%、回収率は80%前後
                            </div>
                        </div>
                        <div class="timeline_item">
                            <div class="time">
                                <span class="time_day">day</span>
                                <span class="time_month">now</span>
                            </div>
                            <div class="desc">
                                <p class="flag">開発状況</p>
                                開発は終えることなく、日々アップデートし続けている
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- question -->
        <div class="contents">
            <h3 class="cp_h3title">Question</h3>
            <div class="cp_qa">
                <div class="cp_actab">
                    <input id="cp_tabfour031" type="checkbox" name="tabs">
                    <label for="cp_tabfour031" class="question">このサイトは無料で利用できますか？</label>
                    <div class="cp_actab-content">
                        <p>はい。現時点では全サービス無料で利用可能です。</p>
                        <p>今後、利用者の増加や的中率の増加がみられる場合有料化する可能性もあります。</p>
                    </div>
                </div>
                <div class="cp_actab">
                    <input id="cp_tabfour032" type="checkbox" name="tabs">
                    <label for="cp_tabfour032" class="question">保証や返金対応はありますか？</label>
                    <div class="cp_actab-content">
                        <p>いいえ、ございません。</p>
                        <p>当サイトではAIの予想を公開しているだけですので、外れた際の返金や保証等の対応はしておりません。
                            <br>自己責任でお願いします。
                        </p>
                    </div>
                </div>
                <div class="cp_actab">
                    <input id="cp_tabfour033" type="checkbox" name="tabs">
                    <label for="cp_tabfour033" class="question">AIの予想はいつ公開されますか？</label>
                    <div class="cp_actab-content">
                        <p>レース前日の18時ごろに公開されます。</p>
                        <p>詳しくは２段階に分けて予測を行います。レース前日の18時ごろに最低限の情報のみで予測したAI予想を公開します。馬体重は発走時間の30分ほど前に公開されるので、馬体重やその他各データを加味したAI予想は発走時間の30分前に更新されます。
                        </p>
                    </div>
                </div>
            </div>

        </div>
        </div>

        <div class='top-mainimg'>
            <div class="mainimg">
                <h3>AI Information</h3>
                <p>The main language we used is python. </p>
                <p>Python version is 3.12</p>
            </div>
        </div>

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
    <script src="./js/4-1-2.js"></script>
</body>

</html>