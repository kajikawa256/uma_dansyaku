<?php

$fullpath="/Users/2220077/AppData/Local/Programs/Python/Python311/python ";
$command= $fullpath . "../python/result.py";
exec($command,$output);

$json = mb_convert_encoding($output, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$result_list = json_decode($json[0], true);
var_dump($result_list)

?>