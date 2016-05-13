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
    $passphrase['c'] = $res[0]['secret'];
    $iv = md5("\x1B\x3C\x58".$passphrase['b'], true).md5("\x1B\x3C\x58".$passphrase['b'], true);
    $key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 24);
    $passphrase['a'] = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($passphrase['c']), MCRYPT_MODE_CBC, $iv), "\0\3");
} else {
    $passphrase['a'] = $_GET['password'];
}

$iv = md5("\x1B\x3C\x58".$passphrase['a'], true).md5("\x1B\x3C\x58".$passphrase['a'], true);
$key = substr(md5("\x2D\xFC\xD8".$passphrase['a'], true).md5("\x2D\xFC\xD9".$passphrase['a'], true), 0, 24);
$opts = array('iv' => $iv, 'key' => $key);
$fp = fopen('./file/'.$res[0]['realname'].'.data', 'rb');
$fp_filter = stream_filter_append($fp, 'mdecrypt.rijndael-256', STREAM_FILTER_READ, $opts);


$size = 4096;
$pos = 0;
$buffer = "";

while ($pos < $res[0]['size']) {
    if($pos != 0){
        stream_filter_remove($fp_filter);
        unset($key);
        unset($iv);
        unset($opts);
        $iv = stream_get_contents($fp, mcrypt_get_block_size(MCRYPT_RIJNDAEL_256, 'cbc'), $pos - mcrypt_get_block_size(MCRYPT_RIJNDAEL_256, 'cbc'));
        $key = substr(md5("\x2D\xFC\xD8".$passphrase['a'], true).md5("\x2D\xFC\xD9".$passphrase['a'], true), 0, 24);
        $opts = array('iv' => $iv, 'key' => $key);

        $fp_filter = stream_filter_append($fp, 'mdecrypt.rijndael-256', STREAM_FILTER_READ, $opts);
    }
    $buffer = stream_get_contents($fp, $size, $pos);
    $pos += $size;
    if($pos > $res[0]['size']){
        echo @substr($buffer, 0, ($res[0]['size'] % $size));
    }else{
        echo $buffer;
    }
}
