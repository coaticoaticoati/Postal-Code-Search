<?php
ini_set("display_errors", "OFF");

$zip_value = (int)$_GET["zip"];

// 未入力の場合
if (!isset ($zip_value)) {
    $error_empty = ["message" => "必須パラメータが指定されていません。", "results" => null, "status" => 400];
    $errEmpty_json = json_encode($error_empty, JSON_UNESCAPED_UNICODE);
    echo $errEmpty_json;

// 数字7桁でない場合
} elseif (!preg_match("/\A\d{7}\z/u", $zip_value)) {
    $error_7dig = ["message" => "数字7桁で入力してください。", "results" => null, "status" => 400];
    $err7dig_json = json_encode($error_7dig, JSON_UNESCAPED_UNICODE);
    echo $err7dig_json;

} else {                                    
    try {
        $user = "xxxx";
        $password = "xxxx";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_MULTI_STATEMENTS => false
        ];
        $dbh = new PDO('mysql:host=xxxx;dbname=xxxx', $user, $password, $opt);
         // 入力された郵便番号を検索
        $sql = 'SELECT zipcode, prefecture_kanji, city_kanji, street_kanji FROM zip_all 
        WHERE zipcode = :zip_value';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':zip_value', $zip_value);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $results[] = $row;
        }

    } catch (PDOException $e) {
        echo "エラー:{$e->getMessage()}"; // 本番環境では消す
    }        

    // データと照合し、該当する住所全てを$OK_zipに代入する
    foreach ($results as $result) { 
        if ($result["zipcode"] === $zip_value) { 
            $OK_zip[] = $result;   
        }  
    }    
    // 7桁だがデータに無い場合
    if ($OK_zip === null) { //$OK_zipには何もはいっていない
        $noData = ["message" => "必須パラメータが指定されていません。正しく入力してください。", "results" => null, "status" => 400];
        // $noDataをJSON形式に変換する
        $noData_json = json_encode($noData, JSON_UNESCAPED_UNICODE);
        echo $noData_json;
    } else {
        $OK_zip = ["message" => null, "results" => $OK_zip, "status" => 200];
        // $OK_zipをJSON形式に変換する 
        $OK_zip_json = json_encode($OK_zip, JSON_UNESCAPED_UNICODE);
        echo $OK_zip_json;
    }
}

        