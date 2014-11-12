<?php 
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
header("Content-Type:text/html; charset=utf-8");
include('class/sql.class.php');
$config['sql']['host'] = 'localhost'; // MySQL Host
$config['sql']['dbname'] = 'dbname'; // MySQL Database Name
$config['sql']['username'] = 'root'; // MySQL Username
$config['sql']['password'] = 'root'; // Password for the user
$db = new MySQL($config['sql']['dbname'], $config['sql']['username'], $config['sql']['password'], $config['sql']['host']);
$GLOBALS['db'] = $db;
?>