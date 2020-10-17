<?php
/*
 * 環境変数設定
 */
putenv('APP_DIR='.dirname(__FILE__));
putenv('APP_SECRET=SetA32-byteRandomCharacterString');

/*
 * 環境設定を返す
 */
return
[
    'version' => '1.0.0',
    // データベース接続先設定
    'database' =>[
        'host' => "localhost",
        'username' => "sunlight",
        'password' => "sunlight",
        'database_name' => "sunlight_db",
        'port' => "3306",
        'initial_statements'=> [
            'set names utf8',
        ],
    ],
    // SQLファイル設定
    'sql_file' =>[
        'path' => dirname(__FILE__) . "/sql",
    ],
];
