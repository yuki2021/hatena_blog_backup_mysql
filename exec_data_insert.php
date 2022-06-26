<?php

require('./hatena_data_get.php');
require('./add_data_mysql.php');



if(isset($argv[1]))  {
    if($argv[1] == 'all_get') {
        all_get_hatena_data();
    } elseif($argv[1] == 'update') {
        update_hatena_data();
    } else {
        print('スクリプトの実行にはオプションが必要です。「all_get」で全データ取得。「update」で差分データ取得です。');
    }
 } else {
    print('スクリプトの実行にはオプションが必要です。「all_get」で全データ取得。「update」で差分データ取得です。');
 }

// 全データ取得
function all_get_hatena_data() {

    $getDataObj = new HatenaDataGet();
    $setDataObj = new AddDataMysql();
    
    $setDataObj->clearDB();

    while(true) {
        $temp_arr = $getDataObj->getData();
        if($temp_arr == null || count($temp_arr) == 0) {
            break;
        }
        foreach($temp_arr as $loop) {
            print_r($loop);
            $setDataObj->insertHatenaData($loop);
        }
        sleep(1);
    }
}

// 差分データ取得
function update_hatena_data() {
    $getDataObj = new HatenaDataGet();
    $setDataObj = new AddDataMysql();
}