<?php
// テキストタイプ
define("TEXT_TYPE_NO_RESULT",    0); // 結果無し
define("TEXT_TYPE_EXEC_RESULT",  1); // 実行結果
define("TEXT_TYPE_QUERY_RESULT", 2); // 検索結果
define("TEXT_TYPE_ERROR",       -1); // エラー

/**
 * SQLスクリプトの実行
 *
 * @param PDO $db　PDOオブジェクト
 * @param string[] $sql_text SQL文の配列
 * @return array 実行結果
 */
function executeSqlScript(PDO $db, array $sql_text=[])
{
    $json = new stdClass();
    $json->error = 0;
    $json->execTime = '0';
    $json->lines = [];

    $sqltime = 0;
    foreach( $sql_text as $sql )
    {
        try
        {
            // 空行の出力
            if ($sql && ($sql[0]=='#'))
            {
                $json->lines[] = OutputNoResult(substr($sql,1));
                continue;
            }

            // 特別なEVAL文の実行
            if ( preg_match( "/^eval\s+(.+)/i", $sql, $reg ) )
            {
                ob_start();
                $ret = eval("{$reg[1]};");
                if (isset($ret))
                {
                    $json->lines[] = OutputExecResult($sql, $ret);
                    ob_end_clean();
                }
                else
                {
                    $out = ob_get_clean();
                    if ($out)
                    {
                        $json->lines[] = OutputExecResult($sql, $out);
                    }
                    else
                    {
                        $json->lines[] = OutputNoResult($sql);
                    }
                }
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
            elseif($sql)
            {
                $db->exec($sql);
                $info = $db->errorInfo();
                if(!empty($info) && $info[0]!=='00000')
                {
                    $json->lines[] = OutputError($sql, $info[2]??'exec() error');
                    break;
                }
                $json->lines[] = OutputExecResult($sql, 'ok');
            }
            $time2 =  microtime_as_float();
            $sqltime += ($time2-$time1);
        }
        catch(Throwable $e)
        {
            $json->lines[] = OutputError($sql, $e->getMessage());
            break;
        }
    }
    $json->execTime = sprintf('%01.03f', $sqltime);
    return $json;
}

/**
 * マイクロ秒取得
 *
 * @return float
 */
function microtime_as_float()
{
    list($usec, $sec) = explode(' ', microtime());
    return ((float)$sec + (float)$usec);
}

/**
 * SELECT文の実行
 *
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
        return OutputError($sql, $info[2]??'query() error');
    }

    $arr=$sth->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($arr))
    {
        $line = outputJsonLine($sql, TEXT_TYPE_QUERY_RESULT, $arr);
    }
    else
    {
        $line = outputJsonLine($sql, TEXT_TYPE_QUERY_RESULT, []);
    }
    return $line;
}

/**
 * 結果無しの出力
 *
 * @param string $command コマンド
 */
function OutputNoResult($command)
{
    return outputJsonLine($command, TEXT_TYPE_NO_RESULT);
}

/**
 * 実行結果の出力
 *
 * @param string $command コマンド
 * @param string $result 結果
 */
function OutputExecResult(string $command, string $result)
{
    return outputJsonLine($command, TEXT_TYPE_EXEC_RESULT, $result);
}

/**
 * エラーの出力
 *
 * @param string $command コマンド
 * @param string $result 結果
 */
function OutputError(string $command, string $result)
{
    return outputJsonLine($command, TEXT_TYPE_EXEC_RESULT, $result);
}

/**
 * JSON result出力
 *
 * @param string $command コマンド
 * @param int $type 結果タイプ
 * @param string $result 結果データ
 * @return stdClass JSON Line
 */
function outputJsonLine(string $command, int $type, $result=null)
{
    $obj = new stdClass();
    $obj->command = $command;
    $obj->type = $type;
    $obj->result = $result;
    return $obj;
}