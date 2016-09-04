<?php

/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include 'config.php';
if (!session_id()) {
    session_start();
}

function base64url_decode($data) { 
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
} 

$res = $db->select('file', array('id' => $_GET['id'], 'recycle' => '0'));
if (!$_SESSION['login'] || $_SESSION['username'] != $res[0]['owner']) {
    if ($_SERVER['HTTP_REFERER'] != $config['url'].'downfile.php?id='.$_GET['id'].'&password='.$_GET['password']) {
        header('Location: '.$config['url'].'downfile.php?id='.$_GET['id'].'&password='.$_GET['password']);
        exit();
    }
    if (isset($_GET['download']) && $_GET['download'] == 'true') {
        exit();
    }
}

/* 設定 Header */

header('Content-Type: application/octet-stream');
header('Content-Transfer-Encoding: binary');
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename="'.$res[0]['name'].'"');


/* 檢查是否有設定檔案密碼，如果沒有就直接傳檔案出去 */
if($res[0]['secret'] == ''){
    $fp = fopen('./file/'.$res[0]['realname'].'.data', 'rb');

    $size = 4096;
    $pos = 0;

    while ($pos < $res[0]['size']) {
        echo stream_get_contents($fp, $size, $pos);
        $pos += $size;
    }

    exit();
}


/* Decode Phrase */
if (isset($_GET['download']) && $_GET['download'] == 'true') {
    $passphrase['b'] = $_SESSION['password'];
    $passphrase['c'] = base64_decode($res[0]['secret']);
    $iv = substr(md5("\x1B\x3C\x58".$passphrase['b'], true).md5("\x1B\x3C\x58".$passphrase['b'], true), 0 ,16);
    $key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 32);
    $passphrase['a'] = openssl_decrypt($passphrase['c'], 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    unset($key);
    unset($iv); 
} else {
    $passphrase['a'] = base64url_decode($_GET['password']);
}

$key = substr($passphrase['a'], 0, 32);
$iv = substr($passphrase['a'], 32, 16);

$fp = fopen('./file/'.$res[0]['realname'].'.data', 'rb');//打開檔案
$fsize = fstat($fp)['size'];
$size = 128; //每 128 bytes 為一個 loop
$pos = 0; //初始化
$buffer = "";
while ($pos < $fsize) {
    $rbuf = fread($fp, $size + 16);
    //解密
    $buffer = openssl_decrypt($rbuf,  'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    if ($buffer === false) {
        //echo openssl_error_string() . "\n"; exit();
    }
    echo $buffer;
    flush();
    @ob_flush();

    //讀取 cipher 中上一輪的最後 16 bytes 作為下一輪的 IV
    $iv = substr($rbuf, -16);
    //噴出資料
    $pos += $size;
}
