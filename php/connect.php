<?php
// db.phpを読み込む
 include("db.php");

//与えられた値をinsert文に挿入する
function INSERT($table,$column,$value){
    try{
        //DB接続
        $db = db_connect();

        foreach($value as $data){
            //初期値
            $insertData = "";
            $count = 0;
            $race_count = 12;

            foreach ($data[0] as $item){
                $count ++;
                if (is_int($item)) {
                    // int型の場合、そのまま追加
                    $insertData = $insertData . $item . ", ";
                } elseif (is_string($item)) {
                    // str型の場合、' 'をつけずにそのまま追加
                    $insertData = $insertData."'". $item . "'" . ", ";
                } 

                if (($table == "RESULT_HORSE" and $count % 5 == 0) or ($table == "RACE" and $count == $race_count)){
                    $insertData= substr($insertData, 0, -2); // 最後の文字を削除
           
                    //INSERT 文を生成
                    $sql = "INSERT INTO $table VALUES ($insertData);";
                    echo $sql;
                
                    // SQL を実行
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $insertData = "";
                }
            }
        }
        $db->commit();
    }catch(PDOException $p){
        $db->rollBack();
        exit("DBエラー".$p->getMessage());
    }
}   
?>