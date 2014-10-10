<?php
if(!session_id()) session_start();
include('config.php'); 
function login($username,$password){
	$password=md5($password);
	$res = $GLOBALS['db']->select('user',array('name'=>$username,'pass'=>$password));
	return is_array($res);
}

$username = $_POST['name'];
$password = $_POST['password'];
$res = login($username, $password);
switch($res){
	case 0:	
		header("Location: login.php?err=1");
	break;

	case 1:
		$_SESSION['login'] = true;
		$_SESSION['username'] = $username;
		header("Location: home.php");
	break;
	
	default:
		header("Location: login.php?err=0");
	break;
}


?>