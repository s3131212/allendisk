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
if (!$_SESSION['login']) {
    exit();
}
@ignore_user_abort(false);
@set_time_limit(0);
error_reporting(E_ALL);
if (!isset($_POST['file'])) {
    exit();
}
$id = $_POST['file'];
$file = $db->select('file', array( 'id' => $id ));

/* Create Key */
/*
    $passphrase['a'] 是檔案加密用的 Key
    $passphrase['b'] 是位檔案加密的 Key 作加密所使用的密碼，來自使用者的密碼
    $passphrase['c'] 由 a, b 算出，儲存在資料庫
*/
$passphrase['a'] = sha1(md5(mt_rand().uniqid()));
$passphrase['b'] = $_SESSION['password'];
$iv = md5("\x1B\x3C\x58".$passphrase['b'], true).md5("\x1B\x3C\x58".$passphrase['b'], true);
$key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 24);
$passphrase['c'] = rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $passphrase['a'], MCRYPT_MODE_CBC, $iv)), "\0\3");
unset($key);
unset($iv);

$iv = md5("\x1B\x3C\x58".$passphrase['a'], true).md5("\x1B\x3C\x58".$passphrase['a'], true);
$key = substr(md5("\x2D\xFC\xD8".$passphrase['a'], true).md5("\x2D\xFC\xD9".$passphrase['a'], true), 0, 24);
$opts = array('iv' => $iv, 'key' => $key);

$fp = fopen('./file/'.$file[0]['realname'].'.temp', 'rb');
$size = 4096;
$pos = 0;
while ($pos < $file[0]['size']) {
    $dest = fopen('./file/'.$file[0]['realname'].'.data', 'a+b');
    if($pos != 0){
        $iv = stream_get_contents($dest, mcrypt_get_block_size(MCRYPT_RIJNDAEL_256, 'cbc'), $pos-mcrypt_get_block_size(MCRYPT_RIJNDAEL_256, 'cbc'));
        $key = substr(md5("\x2D\xFC\xD8".$passphrase['a'], true).md5("\x2D\xFC\xD9".$passphrase['a'], true), 0, 24);
        $opts = array('iv' => $iv, 'key' => $key);
    }
    $dest_filter = stream_filter_append($dest, 'mcrypt.rijndael-256', STREAM_FILTER_WRITE, $opts);
    $write = stream_copy_to_stream($fp, $dest, $size, $pos);
    $pos += $write;
    fclose($dest);
}
fclose($fp);

@unlink('./file/'.$file[0]['realname'].'.temp');

$result = $db->update('file', array('secret'=>$passphrase['c']), array('id'=>$id));
echo 'success';