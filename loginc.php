<?php

/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
if (!session_id()) {
    session_start();
}
include 'config.php';
include dirname(__FILE__).'/class/password_compat.php';
function login($username, $password)
{
    $res = $GLOBALS['db']->select('user', array('name' => $username));

    /* For update from 1.4 */
    /* This code will be removed in 1.6 */
    if (strlen($res[0]['pass']) == 32) {
        if ($res[0]['pass'] == md5($password)) {
            $GLOBALS['db']->update('user', array('pass' => password_hash($password, PASSWORD_DEFAULT)), array('name' => $username));
            $passphrase['b'] = md5_128($password);
            $iv = substr(md5("\x1B\x3C\x58".$passphrase['b'], true), 0, 8);
            $key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 24);
            foreach ($GLOBALS['db']->select('file', array('owner' => $username)) as $value) {
                $passphrase['a'] = $value['secret'];
                $passphrase['c'] = rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $passphrase['a'], MCRYPT_MODE_CBC, $iv)), "\0\3");
                $GLOBALS['db']->update('file', array('secret' => $passphrase['c']), array('id' => $value['id']));
            }

            return true;
        } else {
            return false;
        }
    }
    /* Update Code End */

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
        $_SESSION['username'] = $username;
        $_SESSION['password'] = md5_128($password);
        echo 2;
    break;

    default:
        echo 0;
    break;
}
