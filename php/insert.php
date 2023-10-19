<?php

// $command = "C:/Users/2220077/AppData/Local/Programs/Python/Python311/python ../python/result.py";
// $command = "C:/Users/2220200/AppData/Local/Programs/Python/Python312/python ../python/result.py";

exec($command, $output);

$json = mb_convert_encoding($output, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$result_list = json_decode($json[0], true);

// var_dump($result_list[0]);
/*
  配列メモ
  １開催ずつ取れる
  $result_list[何日目][何レース目][0がレース結果,0+1がレース情報,0+2が払い戻し][詳細]
*/

var_dump($result_list);

// 結果情報テーブル
// $result_information = [];
// for ($i = 0; $i < count($result_raceid); $i++) {
//   foreach ($result_list as $id) {
//     for ($j = 0; $j = count($id); $j += 45) {
//       // $result_information[$i] = "('" . $result_raceid[$i] . "','" . $id[$j] . "'," . $id[$j + 1] . "," . $id[$j + 2] . "," . $id[$j + 3] . ")";
//       echo $result_raceid[$i];
//       // echo $id[$j];
//       // echo $id[$j + 1];
//       // echo $id[$j + 2];
//       // echo $id[$j + 3];
//     };
//   }
// }

// echo $result_information;

