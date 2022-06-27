# hatena_blog_backup_mysql

はてなブログのデータをAPIを使って取得してMySQLに保存します。

## 動作環境

- PHP 7.4.29
- PDO
- MySQL 5.7
- Linuxかmac osの環境であれば動くと思われます。Windowsは動作確認してません。

## インストール方法

1. 使用したいDBの中にhatena_blog_dataというテーブルを作ります。

    添付してあるhatena_blog_data.sqlをインポートしてやればOKです。

    ```
    $ mysql -u username -p database_name < hatena_blog_data.sql
    ```

2. config.phpの内容を書き換える

    ```
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
    ```

    - USERNAME: 使用したいはてなブログのユーザ名を使用してください
    - APIKEY: 管理画面 > 設定 > 詳細設定 > アカウント設定 > そこに書かれているAPIKeyを使用してください
    - BLOGID: 独自ドメインではない、はてなからデフォルトで割り振られたURLを使用してください。
    - $local_ip == '********'  
        使用する環境のIPを使用してください。
    - host: MySQLのアドレスかIPを入力してください。本番環境とテスト環境を使い分けることができます。
    - name: MySQLのデータベース名を入力してください。本番環境とテスト環境を使い分けることができます。
    - username: MYSQLの接続ユーザネームを入力してください。本番環境とテスト環境を使い分けることができます。
    - password: MYSQLの接続パスワードを入力してください。本番環境とテスト環境を使い分けることができます。

## 使い方

```
$ php exec_data_insert.php all_get
```

上記のコマンドで、hatena_blog_dataのテーブルを一旦空にして全部のデータを取得してきます。DBを設置した初回起動時などにお使いください。

```
$ php exec_data_insert.php update
```

DBを確認しながら、その新規データが登録されていなければ追加で保存します。バッチ処理などに加えて、日々の更新分をバックアップするなどにご利用ください。