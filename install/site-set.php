<?php

/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
if (file_exists('install.lock')) {
    header('Location: ../index.php');
    exit();
}
function check_set($key)
{
	if(isset($_POST[$key]) && $_POST[$key] != null)
		return true;
	else
		return false;
}
require '../config.php';
if(!(check_set('sitename') && check_set('sitetitle') && check_set('size') && check_set('url') && check_set('total') && check_set('admin') && check_set('subtitle'))) {
	echo '<script>alert("請確認所有空格都有填寫"); window.history.go(-1);</script>';
	exit();
}
$db->update('setting', array('value' => $_POST['sitename']), array('name' => 'sitename'));
$db->ExecuteSQL(sprintf("UPDATE `setting` SET `value` = '%s' WHERE `setting`.`name` = 'sitetitle';", $db->databaseLink->real_escape_string($_POST['sitetitle'])));
$db->update('setting', array('value' => $_POST['size']), array('name' => 'size'));
$db->update('setting', array('value' => $_POST['url']), array('name' => 'url'));
$db->update('setting', array('value' => $_POST['total']), array('name' => 'total'));
$db->update('setting', array('value' => $_POST['admin']), array('name' => 'admin'));
$db->update('setting', array('value' => $_POST['subtitle']), array('name' => 'subtitle'));

if (isset($_POST['tos']) && $_POST['tos'] != 'true') {
    $tos = 'false';
} else {
    $tos = 'true';
}
$db->update('setting', array('value' => $tos), array('name' => 'tos'));
if (isset($_POST['why']) && $_POST['why'] != 'true') {
    $why = 'false';
} else {
    $why = 'true';
}
$db->update('setting', array('value' => $why), array('name' => 'why'));
if (isset($_POST['reg']) && $_POST['reg'] != 'true') {
    $reg = 'false';
} else {
    $reg = 'true';
}
$db->update('setting', array('value' => $reg), array('name' => 'reg'));
header('location:newuser-setting.php');
