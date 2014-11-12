<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
if(file_exists("install.lock")){
    header("Location: ../index.php");
    exit();
}
require("../config.php"); 
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
if ($username=="") {
    header("Location: newuser-setting.php?err=0");
    exit();
}
if ($email=="") {
    header("Location: newuser-setting.php?err=0");
    exit();
}
if ($password=="") {
    header("Location: newuser-setting.php?err=0");
    exit();
}
$db->insert(array("name"=>$username,"pass"=>md5($password),"email"=>$email),"user");
$myfile = fopen("install.lock", "w");
header("Location: index.php?fin=fin");
?>