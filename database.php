<?php header("Content-Type:text/html; charset=utf-8");
include('class/sql.class.php');
$config['sql']['host'] = 'host';
$config['sql']['dbname'] = 'dbname';
$config['sql']['username'] = 'username';
$config['sql']['password'] = 'password';
$db = new MySQL($config['sql']['dbname'], $config['sql']['username'], $config['sql']['password'], $config['sql']['host']);
$GLOBALS['db'] = $db;
?>