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
require '../config.php';
include '../class/password_compat.php';
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
if ($username == '') {
    header('Location: newuser-setting.php?err=0');
    exit();
}
if ($email == '') {
    header('Location: newuser-setting.php?err=0');
    exit();
}
if ($password == '') {
    header('Location: newuser-setting.php?err=0');
    exit();
}
$db->insert(array('name' => $username, 'pass' => password_hash($password, PASSWORD_DEFAULT), 'email' => $email), 'user');
$install_token = fopen('install.lock', 'w');
fclose($install_token);
header('Location: index.php?fin=fin');
