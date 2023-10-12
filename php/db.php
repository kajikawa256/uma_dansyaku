<?php
function db_connect()
{
  $host = 'データベースのホスト名';
  $dbname = 'データベース名';
  $username = 'ユーザー名';
  $password = 'パスワード';

  try {
    return new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  } catch (PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
  }
};
