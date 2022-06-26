<?php
// はてなブログAPIの設定
define("USERNAME","*************");
define("APIKEY","*************");
define("BLOGID","******.hatenablog.com");//example.hatenadiary.com

// DB接続先
$local_ip = getHostByName(getHostName());
if($local_ip == '********') {
    // 本番環境
    $dbConfig = [
        'host' => '****************',
        'name' => '****************',
        'username' => '********',
        'password' => '********',
    ];
} else {
    // テスト環境
    $dbConfig = [
        'host' => '**********',
        'name' => '**************',
        'username' => '****',
        'password' => '****',
    ];
}
