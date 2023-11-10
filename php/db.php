<?php
function db_connect()
{

  $host = 'localhost';
  $dbname = 'umadan';
  $username = 'umadan';
  $password = 'umadan';

  try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db -> setAttribute(PDO::ERRMODE_SILENT ,false);
    $db->beginTransaction();
    return $db;

  } catch (PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
  }
};
