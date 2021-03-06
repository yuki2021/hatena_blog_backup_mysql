<?php

require('./config.php');
require('./vendor/autoload.php');

class AddDataMysql {

    private $db;
    private $dbConfig;

    /// コンストラクタ
    public function __construct() {

        $this->dbConfig = $GLOBALS['dbConfig'];

        // DB接続クラス取得
        $this->db = new \Hadi\Database();
        $this->db->connect($this->dbConfig);
    }

    /// デストラクタ
    public function __destruct() {
        $this->db->disconnect();
    }

    /// 渡された配列のデータをDBにインサートする
    public function insertHatenaData($set_array) {
        $this->db->table('hatena_blog_data')->insert($set_array);
    }

    /// データベースをTRUNCATE
    public function clearDB() {
        $this->db->table('hatena_blog_data')->truncate();   
    }

    /// データベースに渡された配列の値が存在するか確認する
    public function checkExistData($temp_arr) {
        $count = $this->db->table('hatena_blog_data')->select([
            'field' => ['*'],
            'condition' => 'WHERE blog_id = "'. $temp_arr['blog_id'] .'"',
        ])->total();
        print_r($count);
        if($count == 0) {
            return true;
        } else {
            return false;
        }
    }
}

