<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
header("Content-Type:text/html; charset=utf-8");
include 'class/sql.class.php';
$config['sql']['host'] = '%s';
// MySQL Host
$config['sql']['dbname'] = '%s';
// MySQL Database Name
$config['sql']['username'] = '%s';
// MySQL Username
$config['sql']['password'] = '%s';
// Password for the user
$db = new MySQL($config['sql']['dbname'], $config['sql']['username'], $config['sql']['password'], $config['sql']['host']);
$GLOBALS['db'] = $db;
?>
