<?php
$command = "C:/Users/2220200/AppData/Local/Programs/Python/Python312/python ../python/result.py";
exec($command, $output);

$json = mb_convert_encoding($output, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');

/*
  １開催ずつ取れる
  $result_list[何日目][何レース目][0がレース結果,0+1がレース情報,0+2が払い戻し][詳細]
*/
$result_list = json_decode($json[0], true);

$result_race = [];    // レース結果
$result_info = [];    // レース情報
$result_pay = [];     // 払い戻し

// $result_listの中身をレース結果、レース情報、払い戻しごとにわける
for ($i = 0; $i < count($result_list); $i++) {    // $i = 何日目か
  for ($j = 0; $j < count($result_list); $j++) {
    $result_race[] = $result_list[$i][$j][0];
    $result_info[] = $result_list[$i][$j][1];
    $result_pay[] = $result_list[$i][$j][2];
  }
}

