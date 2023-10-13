<?php
$command = "C:/Users/2220200/AppData/Local/Programs/Python/Python312/python ../python/result.py";
exec($command, $output);

$json = mb_convert_encoding($output, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$result_list = json_decode($json[0], true);
$result_raceid = array_keys($result_list);

// $count = 0;
// for ($j = 0; $j < count($result_raceid); $j++) {
//   foreach ($result_list as $id) {
//     echo "レースID" . $result_raceid[$j] . "\n";
//     for ($i = 0; $i < count($id); $i += 2) {
//       echo "馬番" . $id[$i] . "\n";
//       echo "馬名" . $id[$i + 1] . "\n";
//       echo "馬枠" . $id[$i + 2] . "\n";
//     }
//     echo "\n";
//   }
// }

// 結果情報テーブル
$result_information = [];
for ($i = 0; $i < count($result_raceid); $i++) {
  foreach ($result_list as $id) {
    for ($j = 0; $j = count($id); $j += 3) {
      $result_information[$i] = "('" . $result_raceid[$i] . "','" . $id[$j] . "'," . $id[$j + 1] . "," . $id[$j + 2] . "," . $id[$j + 3] . ")";
    };
  }
}

echo $result_information;
