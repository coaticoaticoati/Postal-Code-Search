<?php
ini_set("display_errors", "OFF");

// 数字7桁で入力されているか確認する
$number = preg_match("/\A\d{7}\z/u", $_GET["zip"]);

// URLを読み込み、function.phpからデータを取得する
$url = "http://localhost/postalcodesearch/function.php?zip=".$_GET["zip"];
$response = file_get_contents($url);
// 受け取ったJSON形式のデータをデコードする
$response = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>検索結果</title>
        <link rel="stylesheet" href="stylesheet.zipcode.css">
    </head>
    <body>
        <div class="zipcode">
            <div class="container">
                <div class="">
                    <?php if (!$number) : ?>    
                        <p class="error">郵便番号は数字7桁で入力してください。ハイフンは入力しないでください。</p>
                        <?php exit; ?>
                    <?php endif; ?>    

                    <?php if ($response["results"] === null) : ?>
                        <p class="error">郵便番号から住所を取得できませんでした。郵便番号を正しく入力してください。</p>
                        <?php exit; ?>
                    <?php else : ?>
                        <?php $count = count($response["results"]) ;?>
                        <p>検索結果：<?= $count ?>件</p>
                        <p>郵便番号：<?= $response["results"][0]["zipcode"] ?>の住所は以下のとおりです</p>
                        <?php foreach ($response["results"] as $value) : ?> 
                            <p><?= "{$value["prefecture_kanji"]}　{$value["city_kanji"]}　{$value["street_kanji"]}" ?></p>
                        <?php endforeach ?>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </body>        
</html>