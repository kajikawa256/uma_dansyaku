<?php
function db_connect()
{

  // $host = 'umadan-db.cg4rvknxvwgf.us-east-2.rds.amazonaws.com';
  // $dbname = 'umadan';
  $username = 'root';
  $password = 'root';
  $host = 'localhost';
  $dbname = 'umadan';
  // $username = 'root';
  // $password = 'Se2aoguxuh';


  try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ERRMODE_SILENT, false);
    $db->beginTransaction();
    return $db;
  } catch (PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
  }
};
