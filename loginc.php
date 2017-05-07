<?php

/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/

include 'config.php';
if (!session_id()) {
    session_start();
}
include dirname(__FILE__).'/class/password_compat.php';
function login($username, $password)
{
    $res = $GLOBALS['db']->select('user', array('name' => $username));

    if (password_verify($password, $res[0]['pass'])) {
        return true;
    } else {
        return false;
    }
}

function md5_128($text)
{
    for ($i = 0; $i < 128; ++$i) {
        $text = md5($text);
    }

    return $text;
}

$username = $_POST['name'];
$password = $_POST['password'];
$res = login($username, $password);
switch ($res) {
    case 0:
        echo 1;
    break;

    case 1:
        $_SESSION['login'] = true;
        $_SESSION['username'] = htmlspecialchars($username);
        $_SESSION['password'] = md5_128($password);
        echo 2;
    break;

    default:
        echo 0;
    break;
}
