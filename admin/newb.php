<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
require("../require.php");
_session_start();

if( $_SESSION["alogin"] ){
    $nameCheckList = [ "username", "email", "password" ];
    foreach ($nameCheckList as $key => $value) {
        $$value = ( isset( $_POST[$value] ) ) ? $_POST[$value] : "";
    }

    $namecheck = $db->ExecuteSQL( sprintf(
        "SELECT count(*) AS `count`  FROM `user` WHERE `name` = '%s'",
        $db->SecureData( $username ) ) );

    if( $namecheck[0]["count"] > 0 ){
        $go = "newuser.php?err=2";
    } else {
        foreach ($nameCheckList as $key => $value) {
            if ($$value == "") {
                $go = "newuser.php?err=0";
            }
        }
        if( !isset( $go ) ) {
            $db->insert(
                        [ "name" => $username,
                          "pass" => password_hash($password, PASSWORD_DEFAULT),
                          "email"=> $email ],
                          "user");
            $go = "newuser.php?s=1";
        }
    }
} else {
    $go = "login.php";
}

header( "Location: {$go}" );
exit;