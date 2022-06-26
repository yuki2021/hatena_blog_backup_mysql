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
    function __destruct() {
        $this->db->disconnect();
    }
}

$dbObj = new AddDataMysql();

