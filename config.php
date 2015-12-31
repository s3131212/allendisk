<?php

/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/

include 'database.php';
error_reporting(0);
//error_reporting(E_ALL);

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

$reg = $db->select('setting', array('name' => 'reg'));
if ($reg[0]['value'] == 'true') {
    $config['reg'] = true;
} else {
    $config['reg'] = false;
}

$why = $db->select('setting', array('name' => 'why'));
if ($why[0]['value'] == 'true') {
    $config['why'] = true;
} else {
    $config['why'] = false;
}

$tos = $db->select('setting', array('name' => 'tos'));
if ($tos[0]['value'] == 'true') {
    $config['tos'] = true;
} else {
    $config['tos'] = false;
}

/* Session Directory Praser */
//session_set_cookie_params(0, '/' . parse_url($config['url'])['path']);
//setcookie('PHPSESSID',session_id(),0, '/' . parse_url($config['url'])['path']);
//session_name(preg_replace("/[^a-zA-Z0-9]+/", "", $config['url']));
if (!isset($_COOKIE['session_name'])) {
    $session_name = uniqid('allendisk');
    $dir = explode('/', dirname($_SERVER['SCRIPT_NAME']));
    if (end($dir) == 'admin') {
        array_pop($dir);
    }
    setcookie('session_name', $session_name, time() + (60 * 60 * 24 * 5), implode($dir, '/'));
    //$_COOKIE['session_name'] = $session_name;
    session_name($session_name);
} else {
    session_name($_COOKIE['session_name']);
}
