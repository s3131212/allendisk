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
@set_time_limit(0);
error_reporting(E_ALL);
if (!isset($_POST['file'])) {
    exit();
}
$id = $_POST['file'];
$file = $db->select('file', array( 'id' => $id ));

/* 檢查需不需要加密 */
if(!$config['encrypt_file']){
    rename('./file/'.$file[0]['realname'].'.temp', './file/'.$file[0]['realname'].'.data');
    echo 'success';
    exit();
}

/* Create Key */
/*
    $passphrase['a'] 是檔案加密用的 Key
    $passphrase['b'] 是位檔案加密的 Key 作加密所使用的密碼，來自使用者的密碼
    $passphrase['c'] 由 a, b 算出，儲存在資料庫
*/

$passphrase['a'] = openssl_random_pseudo_bytes(32) . openssl_random_pseudo_bytes(16);
$passphrase['b'] = $_SESSION['password'];
$iv = substr(md5("\x1B\x3C\x58".$passphrase['b'], true).md5("\x1B\x3C\x58".$passphrase['b'], true), 0 ,16);
$key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 32);
$passphrase['c'] = openssl_encrypt($passphrase['a'], 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
unset($key);
unset($iv); 

$key = substr($passphrase['a'], 0, 32);
$iv = substr($passphrase['a'], 32, 16);

$fp = fopen('./file/'.$file[0]['realname'].'.temp', 'rb'); //讀取原始檔案 Plaintext
$file[0]['size'] = fstat($fp)['size'];
$dest = fopen('./file/'.$file[0]['realname'].'.data', 'wb'); // 建立並開啟加密後的檔案 Cipher
$size = 128; //每 128 bytes 為一個 loop
$pos = 0; // 初始化
$buffer = "";
while ($pos < $file[0]['size']) { //在 $pos 跑完整個檔案後才會退出 while
    $buffer = openssl_encrypt(fread($fp, $size), 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv); //加密程序
    fwrite($dest, $buffer, $size + 16); //把內容寫進加密檔 $dest 
    $iv = substr($buffer, -16); //為下一輪 loop 找出 IV
    $pos += $size;
    //登記現在的加密進度，不怎麼重要的東西
    file_put_contents(dirname(__FILE__).'/temp/'.$id.'.txt', (floor(($pos/$file[0]['size'])*100) . '%'));
}
fclose($dest);
fclose($fp);

@unlink('./file/'.$file[0]['realname'].'.temp');
@unlink('./temp/'.$id.'.txt');

$result = $db->update('file', array('secret'=>base64_encode($passphrase['c'])), array('id'=>$id));
echo 'success';