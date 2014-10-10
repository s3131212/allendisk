<?php
require("../config.php"); 
if(!session_id()) session_start();
if($_SESSION["alogin"]){
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$namecheck=$db->ExecuteSQL(sprintf("SELECT count(*) AS `count`  FROM `user` WHERE `name` = '%s'",mysql_real_escape_string($username,$db->databaseLink)));
if($namecheck[0]["count"] > 0){header("Location: newuser.php?err=2"); exit();}
if ($username=="") {
    header("Location: newuser.php?err=0");
    exit();
}
if ($email=="") {
    header("Location: newuser.php?err=0");
    exit();
}
if ($password=="") {
    header("Location: newuser.php?err=0");
    exit();
}
$db->insert(array("name"=>$username,"pass"=>md5($password),"email"=>$email),"user");
header("Location: newuser.php?s=1");
}else{
header("location:login.php");
}
?>