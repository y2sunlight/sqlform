<?php
// テキストタイプ
define("TEXT_TYPE_SQL",          0); // SQL文
define("TEXT_TYPE_EXEC_RESULT",  1); // 実行結果
define("TEXT_TYPE_QUERY_RESULT", 2); // 検索結果
define("TEXT_TYPE_ERROR",       -1); // エラー

/**
 * SQLスクリプトの実行
 * @param mysqli $mysqli　MySQLiオブジェクト
 * @param string[] $sql_text SQL文の配列
 * @return array 実行結果
 */
function executeSqlScript(mysqli $mysqli, array $sql_text=[])
{
    $json = new stdClass();
    $json->lines = [];
    $json->error = 0;
    $json->execTime = '0';

    $sqltime = 0;
    foreach( $sql_text as $sql )
    {
        if ( !$sql ) continue; # 空行

        // SQL文表示
        $json->lines[] = OutputSql($sql);

        // 特別なEVAL文の実行
        if ( preg_match( "/^eval\s+(.+)/i", $sql, $reg ) )
        {
            $ret = eval("{$reg[1]};");
            if (isset($ret)) $json->lines[] = OutputExecResult($ret);
            continue;
        }

        // SELECT文と非SELECT文で処理を分ける
        $time1 =  microtime_as_float();
        if (preg_match("/^(select|show)\s/i", $sql))
        {
            $res = DoSelect($mysqli, $sql);
            $json->lines[] = $res;
            if ($res->type==TEXT_TYPE_ERROR) break;
        }
        else
        {
            $res = $mysqli->query($sql);
            if($res===false)
            {
                $json->lines[] = OutputError($mysqli->error);
                $json->error = 1;
                break;
            }
            $json->lines[] = OutputExecResult('ok');
        }
        $time2 =  microtime_as_float();
        $sqltime += ($time2-$time1);
    }
    $json->execTime = sprintf('%01.03f', $sqltime);
    return $json;
}

/**
 * マイクロ秒取得
 * @return float
 */
function microtime_as_float()
{
    list($usec, $sec) = explode(' ', microtime());
    return ((float)$sec + (float)$usec);
}

/**
 * SELECT文の実行
 * @param mysqli $mysqli　MySQLiオブジェクト
 * @param string $sql SQL文
 * @return stdClass JSON Line
 */
function DoSelect( mysqli $mysqli, string $sql )
{
    // 検索実行
    $res = $mysqli->query($sql);
    if($res===false)
    {
        return OutputError($mysqli->error);
    }

    // 検索実行の出力
    if ($rows = $res->fetch_all(MYSQLI_ASSOC))
    {
        $line = outputJsonLine(TEXT_TYPE_QUERY_RESULT, $rows);
    }
    else
    {
        $line = outputJsonLine(TEXT_TYPE_QUERY_RESULT, []);
    }
    return $line;
}

/**
 * SQL文の出力
 * @param string $text テキスト
 */
function OutputSql($text)
{
    return outputJsonLine(TEXT_TYPE_SQL, $text);
}

/**
 * 実行結果の出力
 * @param string $text テキスト
 */
function OutputExecResult($text)
{
    return outputJsonLine(TEXT_TYPE_EXEC_RESULT, $text);
}

/**
 * エラーの出力
 * @param string $text テキスト
 */
function OutputError($text)
{
    return outputJsonLine(TEXT_TYPE_ERROR, $text);
}

/**
 * JSON Line出力
 * @param int $type Line Type
 * @param string $line Line Data
 * @return stdClass JSON Line
 */
function outputJsonLine(int $type, $line=null)
{
    $obj = new stdClass();
    $obj->type = $type;
    $obj->line = $line;
    return $obj;
}