<?php
// テキストタイプ
define("TEXT_TYPE_SQL",          0); // SQL文
define("TEXT_TYPE_EXEC_RESULT",  1); // 実行結果
define("TEXT_TYPE_QUERY_RESULT", 2); // 検索結果
define("TEXT_TYPE_ERROR",       -1); // エラー

/**
 * SQLスクリプトの実行
 * @param PDO $db　PDOオブジェクト
 * @param string[] $sql_text SQL文の配列
 * @return array 実行結果
 */
function executeSqlScript(PDO $db, array $sql_text=[])
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
            $res = DoSelect($db, $sql);
            $json->lines[] = $res;
            if ($res->type==TEXT_TYPE_ERROR) break;
        }
        else
        {
            $db->exec($sql);
            $info = $db->errorInfo();
            if(!empty($info) && $info[0]!=='00000')
            {
                $json->lines[] = OutputError($info[2]??'exec() error');
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
 * @param PDO $db　PDOオブジェクト
 * @param string $sql SQL文
 * @return stdClass JSON Line
 */
function DoSelect(PDO $db, string $sql)
{
    // 検索実行
    $sth= $db->query($sql);
    if($sth===false)
    {
        $info = $db->errorInfo();
        return OutputError($info[2]??'query() error');
    }

    $arr=$sth->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($arr))
    {
        $line = outputJsonLine(TEXT_TYPE_QUERY_RESULT, $arr);
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