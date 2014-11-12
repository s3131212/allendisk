<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
if(!session_id()) session_start();
include('../config.php'); 
$res = $db->select('setting',array('name'=>"admin"));
if($_POST["password"]==$res[0]["value"]){
	$_SESSION['alogin'] = true;
	header("Location: index.php");
}else{
	header("Location: login.php?err=1");
}
?>