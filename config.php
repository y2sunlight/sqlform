<?php
/*
 * 環境変数設定
 */
putenv('APP_VER=1.0.0');
putenv('APP_DIR='.dirname(__FILE__));
putenv('APP_NO_API_AUTH=1');
putenv('APP_SECRET=SetA32-byteRandomCharacterString');

/*
 * 環境設定を返す
 */
return
[
    // データベース接続先設定
    'database'=>'sqlite',
    'connections' =>
    [
        'mysql' => [
            'dsn' => 'mysql:host=localhost;port=3306;dbname=sunlight_db',
            'username' => 'sunlight',
            'password' => 'sunlight',
            'driver_options' => [PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8'],
            // 'initial_statements'=> ['set names utf8'],
        ],
        'sqlite' => [
            'dsn' => 'sqlite:'.dirname(__FILE__) . '/db/sqlform.sqlite',
            'db_file' => dirname(__FILE__) . '/db/sqlform.sqlite',
        ],
    ],
    // SQLファイル設定
    'sql_file' =>[
        'path' => dirname(__FILE__) . "/sql/{database}",
    ],
];
