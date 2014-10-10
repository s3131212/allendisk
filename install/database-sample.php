<?php header("Content-Type:text/html; charset=utf-8");
include('class/sql.class.php');
$config['sql']['host'] = '%s';
$config['sql']['dbname'] = '%s';
$config['sql']['username'] = '%s';
$config['sql']['password'] = '%s';
$db = new MySQL($config['sql']['dbname'], $config['sql']['username'], $config['sql']['password'], $config['sql']['host']);
$GLOBALS['db'] = $db;
?>