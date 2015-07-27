<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */

require_once( '../require.php' );
_session_start();

$res = $db->select('setting',array('name'=>"admin"));

if( isset( $_POST["password"] ) && isset( $_POST["captcha"] ) &&
        $_POST["password"] == $res[0]["value"] &&
        strtolower( $_POST["captcha"] ) == strtolower( $_SESSION['captcha']['code'] ) ) {
	$_SESSION['alogin'] = true;
	$go = "index.php";
} else {
	$go = "login.php?err=1";
}
header( "Location: {$go}" );
exit;