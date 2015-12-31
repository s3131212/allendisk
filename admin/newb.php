<?php

/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
require '../config.php';
include '../class/password_compat.php';
if (!session_id()) {
    session_start();
}
if (!$_SESSION['alogin']) {
    header('location:login.php');
    exit();
}
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$namecheck = $db->ExecuteSQL(sprintf("SELECT count(*) AS `count`  FROM `user` WHERE `name` = '%s'", $db->SecureData($username)));
if ($namecheck[0]['count'] > 0) {
    header('Location: newuser.php?err=2');
    exit();
}
if ($username == '') {
    header('Location: newuser.php?err=0');
    exit();
}
if ($email == '') {
    header('Location: newuser.php?err=0');
    exit();
}
if ($password == '') {
    header('Location: newuser.php?err=0');
    exit();
}
$db->insert(array('name' => $username, 'pass' => password_hash($password, PASSWORD_DEFAULT), 'email' => $email), 'user');
header('Location: newuser.php?s=1');
