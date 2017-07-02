<?php

/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/

include 'database.php';
//error_reporting(0);
error_reporting(E_ALL);
@ini_set("session.cookie_httponly", 1);

$sitename = $db->select('setting', array('name' => 'sitename'));
$config['sitename'] = $sitename[0]['value'];

$sitetitle = $db->select('setting', array('name' => 'sitetitle'));
$config['sitetitle'] = $sitetitle[0]['value'];

$size = $db->select('setting', array('name' => 'size'));
$config['size'] = $size[0]['value'];

$url = $db->select('setting', array('name' => 'url'));
$config['url'] = $url[0]['value'];

$total = $db->select('setting', array('name' => 'total'));
$config['total'] = $total[0]['value'];

$subtitle = $db->select('setting', array('name' => 'subtitle'));
$config['subtitle'] = $subtitle[0]['value'];

$tos = $db->select('setting', array('name' => 'tos'));
$config['tos'] = $tos[0]['value'];

$why = $db->select('setting', array('name' => 'why'));
$config['why'] = $why[0]['value'];

$reg = $db->select('setting', array('name' => 'reg'));
if ($reg[0]['value'] == 'true') {
    $config['reg'] = true;
} else {
    $config['reg'] = false;
}

$reg = $db->select('setting', array('name' => 'encrypt_file'));
if ($reg[0]['value'] == 'true') {
    $config['encrypt_file'] = true;
} else {
    $config['encrypt_file'] = false;
}

$session_protect = $db->select('setting', array('name' => 'session_protect'));
if ($session_protect[0]['value'] == 'true') {
	$config['session_protect'] = true;
    if(!isset($_COOKIE['session_name'])){
    	$session_name = uniqid('allendisk');
    	$dir = explode('/', dirname($_SERVER['SCRIPT_NAME']));
    	if(end($dir) == 'admin'){
    		array_pop($dir);
    	}
    	setcookie('session_name', $session_name, time()+(60*60*24*5), implode($dir, '/'));
    	session_name($session_name);
    }else{
    	session_name($_COOKIE['session_name']);
    }
}else{
	$config['session_protect'] = false;
}
