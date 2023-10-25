<?php

include("connect.php");
include("def.php");

#使用者の苗字を入力（詳しくはdef.php）
$command = KAJIKAWA;

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

// var_dump($result_list);

// $result_listの中身をレース結果、レース情報、払い戻しごとにわける
// for ($i = 0; $i < count($result_list); $i++) {    // $i = 何日目か
for ($j = 0; $j < count($result_list); $j++) {
  $result_race[] = array($result_list[$j][0]);
  $result_info[] = array($result_list[$j][1]);
  $result_pay[] =  array($result_list[$j][2]);
}
//}

// insert() 関数を呼び出す
// $result = INSERT($table, $columns, $result_info);
// for($i = 0; $i < $table_size; $i++){
//   INSERT($table[$i],$columns[$i],);
// }

INSERT($table[$RESULT_HORSE],$columns[$RESULT_HORSE],$result_list);
INSERT($table[$RACE],$columns[$RACE],$result_info);
INSERT($table[$HIT_DETAIL],$columns[$HIT_DETAIL],$result_pay);

