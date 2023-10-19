<?php
// db.phpを読み込む
 include("db.php");

//与えられた値をinsert文に挿入する
function INSERT($table,$column,$value){
    try{
        //DB接続
        $db = db_connect();

        foreach($value as $data){
            // 各行のデータをカンマ区切りの文字列に変換
            $insertData = implode(', ', array_map(array($db, 'quote'), $data));
        
            // INSERT 文を生成
            $sql = "INSERT INTO $table VALUES ($insertData)";
            echo $sql;
        
            // SQL を実行
            $stmt = $db->prepare($sql);
            $stmt->execute();
            echo "実行成功";
        }
        $db->commit();
    }catch(PDOException $p){
        $db->rollBack();
        exit("DBエラー".$p->getMessage());
    }
}   
?>