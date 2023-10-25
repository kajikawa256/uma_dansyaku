<?php

//pythonパス 定数化しているのでユーザに合わせて変更してください
define("KITADE","C:/Users/2220054/AppData/Local/Programs/Python/Python312/python ../python/result.py");
define("KAJIKAWA","C:/Users/2220077/AppData/Local/Programs/Python/Python311/python ../python/result.py");
define("SHIBATA","C:/Users/2220200/AppData/Local/Programs/Python/Python312/python ../python/result.py");

/*
    定数を定義
*/

//テーブルの配列
$table = ['HIT','RACE','RESULT_HORSE','PREDICTION_HORSE','HIT_DETAIL'];
$columns = [
    ['RACEDATE','COLLECT_PR','HIT_PR'],
    ['RACE_ID', 'RACEDATE','RNAME','RACENUMBER','TIME','DISTANCE','HORSE_TOTAL','GROUND','PLACE','WEATHER','SPIN','SITUATION'],
    ['RACE_ID', 'HNAME', 'HORSENUMBER', 'HORSEFRAME', 'RANKING'],
    ['RACE_ID', 'HNAME', 'HORSENUMBER', 'HORSEFRAME', 'RANKING'],
    ['RACE_ID','KINDS','HORSENUMBER','HORSEFRAME','BETBACK','POPULAR']
];

$table_size = 0;

$HIT = 0;
$RACE = 1;
$RESULT_HORSE = 2;
$PREDICTION_HORSE = 3;
$HIT_DETAIL = 4;

/*

*/

define("race","RACE");

?>