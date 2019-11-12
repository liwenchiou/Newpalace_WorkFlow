<?php

//連接帳號資料表，檢查帳密是否正確
// $db_server = "sql200.web.youp.ga:3306"; //資料庫主機位置
$db_user = 'sa'; //資料庫的使用帳號
$db_password = 'esum'; //資料庫的使用密碼
$db_name = 'NPG'; //資料庫名稱
//PDO的連接語法
try {
    //$pdo = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_password);
    //測試用
    $pdo = new PDO("sqlsrv:Server=192.168.2.111,49227;Database=NPG", $db_user, $db_password);
    //設定為utf8編碼，必要設定
    $pdo->query('SET NAMES "utf8"');
    date_default_timezone_set('Asia/Taipei');
} catch (PDOException $e) {
    // 資料庫連結失敗
    echo '資料庫連線錯誤，請和技術人員聯繫';
}

$db = "oci:dbname=(description=(address=(protocol=tcp)(host=192.168.2.232)(port=1521))(connect_data=(SERVICE_NAME = NEWP)));charset=utf8";
$username = "GOOD";
$password = "SQL";
try {
    $pdo2 = new PDO($db, $username, $password);
} catch (PDOException $e) {
    echo 'ORACLE 連線錯誤';
}

