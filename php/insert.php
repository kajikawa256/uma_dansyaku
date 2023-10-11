<?php

$fullpath="/Users/2220077/AppData/Local/Programs/Python/Python311/python ";
$command= $fullpath . "../python/result.py";
exec($command,$output);

var_dump($output)

?>