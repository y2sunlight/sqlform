<?php
/**
 * Gets an array of SQL statemnets from a file.
 *
 * @param string $filename
 * @return array
 */
function file_get_sql(string $filename):array
{
    if (!file_exists($filename)) return [];
    return array_get_sql(file_get_contents($filename));
}

/**
 * Gets an array of SQL statemnets from text.
 *
 * @param string $text
 * @return array
 */
function array_get_sql(string $text=''):array
{
    $text = str_replace(["\r\n","\r"], "\n", $text);

    // Remove comment
    $text = preg_replace("/\/\*.*?\*\//s", '', $text);
    $text = preg_replace("/--.*?$/m", '', $text);

    // Split SQL text
    $sql = preg_split("/\s*;\s*/", $text);
    array_walk($sql, function(&$item){
        $item = trim($item);
    });
    $sql = array_filter($sql, function($val){
        return !empty(trim($val));
    });
    return $sql;
}

/**
 * Generate an API token.
 *
 * @param string $filename
 * @return string URL
 */
function generate_api_token():string
{
    $str = getenv('APP_SECRET').session_id();
    $_SESSION['API_TOKEN'] = sha1($str);
    return $_SESSION['API_TOKEN'];
}

/**
 * Comfirm the API token.
 *
 * @return bool
 */
function comfirm_api_token():bool
{
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) return false;
    list($bearer, $token) = preg_split('/[\s,]+/', $headers['Authorization']);

    return
        isset($bearer) && (strtolower($bearer) === 'bearer') &&
        isset($token) && ($token === $_SESSION['API_TOKEN']);
}

/**
 * Gets the versioned asset URL.
 *
 * @param string $filename
 * @return string URL
 */
function asset_get(string $filename):string
{
    $modified_at = (@filemtime(getenv('APP_DIR')."/{$filename}"));
    return "{$filename}?v={$modified_at}";
}

