<?php

/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include '../config.php';
if (!session_id()) {
    session_start();
}
$res = $db->select('setting', array('name' => 'admin'));
if ($_POST['password'] == $res[0]['value'] && strtolower($_POST['captcha']) == strtolower($_SESSION['captcha']['code'])) {
    $_SESSION['alogin'] = true;
    header('Location: index.php');
} else {
    header('Location: login.php?err=1');
}
