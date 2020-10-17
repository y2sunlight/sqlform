<?php
/*
 * 設定ファイルの読み込み
 */
$config = require 'config.php';
session_start();

require 'utilities.php';
require 'sqlexec.php';

/*
 * API 認証
 */
 if (!comfirm_api_token()) errorExit('Unauthorized', 401);

/*
 * API コマンド実行
 */
if (!isset($_REQUEST['cmd']))
{
    errorExit('No command');
}

switch($_REQUEST['cmd'])
{
    case "list":
        // Format: cmd=list
        listSqlFiles($config);
        break;

    case "read":
        // Format: cmd=read&f=filename
        readSqlFile($config);
        break;

    case "write":
        // Format: cmd=write&f=filename&t=seqText
        writeSqlFile($config);
        break;

    case "delete":
        // Format: cmd=delete&f=filename
        deleleSqlFile($config);
        break;

    case "execute":
        // Format: cmd=execute&[f=filename|t=seqText]
        ecexuteSql($config);
        break;

    default:
        errorExit('Unkonwn command');
        break;
}

/**
 * SQL実行
 *
 * パラメータ: cmd=execute&[f=filename|t=seqText]
 * JSON:
 * {
 *     "error": 0,
 *     "execTime":"0.123",
 *     "lines": [
 *          {
 *              "type": 0,
 *              "line": "SQL Statement"
 *          }
 *      ]
 * }
 * type: SQL文(0) 実行結果(1) 検索結果(2) エラー(-1)
 *
 * @param array $config
 */

function ecexuteSql(array $config)
{
    $sql_array = [];
    if (isset($_REQUEST['f']))
    {
        // SQLファイル実行
        $sql_file = getSqlPath($config) . "/{$_REQUEST['f']}";
        if (!file_exists($sql_file)) errorExit('File does not exists');

        $sql_array = file_get_sql($sql_file);
    }
    elseif (isset($_REQUEST['t']))
    {
        // SQLスクリプト実行
        $sql_array = array_get_sql($_REQUEST['t']);
    }
    else
    {
        errorExit('No SQL text');
    }

    // データベース接続
    $mysqli = @new mysqli(
        $config['database']['host'],
        $config['database']['username'],
        $config['database']['password'],
        $config['database']['database_name'],
        $config['database']['port']);

    if( $mysqli->connect_errno )
    {
        errorExit($mysqli->connect_errno . ' : ' . $mysqli->connect_error);
    }

    // 初期SQLコマンドの読み込み
    if (isset($config['database']['initial_statements']))
    {
        foreach($config['database']['initial_statements'] as $statement)
        {
            $mysqli->query($statement);
        }
    }

    // SQLスクリプトの実行
    $json = executeSqlScript($mysqli, $sql_array);

    // レスポンス処理
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($json, JSON_PRETTY_PRINT);

    // データベースの切断
    $mysqli->close();
    exit();
}

/**
 * SQLファイル一覧
 *
 * パラメータ: cmd=list
 * JSON:
 * {
 *     "error": 0,
 *     "files": ["sample.sql"]
 * }
 *
 * @param array $config
 */
function listSqlFiles(array $config)
{
    // JSON作成
    $json = new stdClass();
    $json->files = [];
    $json->error = 0;

    // ファイル一覧作成
    foreach(@glob(getSqlPath($config) . '/*.sql') as $file)
    {
        if(is_file($file))
        {
            $json->files[] = basename($file);
        }
    }

    // レスポンス処理
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($json, JSON_PRETTY_PRINT);
    exit();
}

/**
 * SQLファイル読み込み
 *
 * パラメータ: cmd=read&f=filename
 * JSON:
 * {
 *     "error": 0,
 *     "text": ""
 * }
 *
 * @param array $config
 */
function readSqlFile(array $config)
{
    if (!isset($_REQUEST['f'])) errorExit('No file name');
    $sql_file = getSqlPath($config) . "/{$_REQUEST['f']}";

    if (!file_exists($sql_file)) errorExit('File does not exists');

    // JSON作成
    $json = new stdClass();
    $json->error = 0;

    // ファイル読み込み
    $json->text = @file_get_contents($sql_file);

    // レスポンス処理
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($json, JSON_PRETTY_PRINT);
    exit();
}

/**
 * SQLファイル書き込み
 *
 * パラメータ: cmd=write&f=filename&t=seqText[&i]
 * JSON: successful
 * {
 *     "error": 0
 * }
 *
 * JSON 形式: failed
 * {
 *     "error": 1,
 *     "errorMessage": "File already exists",
 * }
 * @param array $config
 */
function writeSqlFile(array $config)
{
    if (!isset($_REQUEST['f'])) errorExit('No file name');
    if (!isset($_REQUEST['t'])) errorExit('No SQL text');

    $filename = $_REQUEST['f'];
    if (pathinfo($filename, PATHINFO_EXTENSION)!='sql') $filename .= '.sql';
    $sql_file = getSqlPath($config) . "/{$filename}";

    // JSON作成
    $json = new stdClass();
    $json->error = 0;

    // 上書き確認
    if (array_key_exists('i',$_REQUEST) && file_exists($sql_file))
    {
        $json->error = 1;
        $json->errorMessage = 'File already exists';
    }
    else
    {
        // ファイル書き込み
        if (false===@file_put_contents($sql_file, $_REQUEST['t']))
        {
            errorExit('Unable to write file');
        }
    }

    // レスポンス処理
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($json, JSON_PRETTY_PRINT);
    exit();
}

/**
 * SQLファイル削除
 *
 * パラメータ:  cmd=delete&f=filename
 * JSON:
 * {
 *     "error": 0
 * }
 *
 * @param array $config
 */
function deleleSqlFile(array $config)
{
    if (!isset($_REQUEST['f'])) errorExit('No file name');
    $sql_file = getSqlPath($config) . "/{$_REQUEST['f']}";

    if (!file_exists($sql_file)) errorExit('File does not exists');

    // JSON作成
    $json = new stdClass();
    $json->error = 0;

    // ファイル書き込み
    if (false===@unlink ($sql_file))
    {
        errorExit('Unable to delete file');
    }

    // レスポンス処理
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($json, JSON_PRETTY_PRINT);
    exit();
}

/**
 * SQL ファイルパスの取得
 *
 * @param array $config
 * @return string
 */
function getSqlPath(array $config)
{
    return ($config['sql_file']['path'] ?? '.');
}

/**
 * エラー終了
 *
 * @param string $message
 * @param int $response_code
 */
function errorExit(string $message='', $response_code=400)
{
    http_response_code($response_code);
    die($message);
}

