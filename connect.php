<?php
/**
 * Connects to database
 *
 * @param array $config
 * @return PDO
 */
function connect_database(array $config):PDO
{
    // Gets the database type
    $database = $config['database'] ?? null;
    if (is_null($database)) throw new Exception('Can not connect');

    // Prepares the database file such as SQLite
    if (isset($config['connections'][$database]['db_file']))
    {
        $db_file = $config['connections'][$database]['db_file'];
        if (!file_exists($db_path=dirname($db_file)))
        {
            @mkdir($db_path, null, true);
        }
    }

    //-------------------------------------------
    // Connects to database
    //-------------------------------------------
    $dsn = $config['connections'][$database]['dsn'];
    $username = $config['connections'][$database]['username'] ?? null;
    $password = $config['connections'][$database]['password'] ?? null;
    $options = $config['connections'][$database]['driver_options'] ?? null;

    $db = new PDO($dsn, $username, $password, $options);

    // Executes initial SQL statements
    $initial_statements = $config['connections'][$database]['initial_statements'] ?? null;
    if (isset($initial_statements))
    {
        foreach((array)$initial_statements as $sql)
        {
            $db->exec($sql);
        }
    }

    // return PDO object
    return $db;
};