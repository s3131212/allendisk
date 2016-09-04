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
//ob_clean();
//@ini_set('error_reporting', E_ALL & ~ E_NOTICE);
//@apache_setenv('no-gzip', 1);
//@ini_set('zlib.output_compression', 'Off');

function base64url_decode($data) { 
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
} 

if(!isset($_GET['password']) && !isset($_GET['id'])){
    header('HTTP/1.1 403 Unauthorized');
    echo 'Permission Denied';
    exit();
}

$res = $db->select('file', array('id' => $_GET['id'], 'recycle' => '0'));

if (isset($_GET['pretoken'])) {
    $pretoken = base64_decode($_GET['pretoken']);
    $pretoken = json_decode($pretoken, 1);
    $token = $pretoken[0];
} else {
    $token['id'] = '';
    $token['time'] = 0;
    $token['dir'] = '123';
}

//驗證檔案
/*if(!($res[0]['owner'] == $_SESSION['username'] || $res[0]['share'] == '1' || ($token['id'] == $_GET['id'] && time() - $token['time'] < 15 && $token['dir'] == $res[0]['dir']))){
    header('HTTP/1.1 403 Unauthorized');
    echo 'Permission Denied';
    exit();
}*/

$passphrase['a'] = base64url_decode($_GET['password']);
$key = substr($passphrase['a'], 0, 32);
$iv = substr($passphrase['a'], 32, 16);

$mime = $res[0]['type']; // The MIME type of the file, this should be replaced with your own.
$size = $res[0]['size']; // The size of the file

// Send the content type header
header('Content-type: ' . $mime);

// Check if it's a HTTP range request
if(isset($_GET['streaming']) && $_GET['streaming'] == 'true' && isset($_SERVER['HTTP_RANGE'])){
    // Parse the range header to get the byte offset
    $ranges = array_map(
        'intval', // Parse the parts into integer
        explode(
            '-', // The range separator
            substr($_SERVER['HTTP_RANGE'], 6) // Skip the `bytes=` part of the header
        )
    );
 
    // If the last range param is empty, it means the EOF (End of File)
    if(!$ranges[1]){
        $ranges[1] = $size - 1;
    }
 
    // Send the appropriate headers
    header('HTTP/1.1 206 Partial Content');
    header('Accept-Ranges: bytes');
    header('Content-Length: ' . ($ranges[1] - $ranges[0])); // The size of the range
 
    // Send the ranges we offered
    header(
        sprintf(
            'Content-Range: bytes %d-%d/%d', // The header format
            $ranges[0], // The start range
            $ranges[1], // The end range
            $size // Total size of the file
        )
    );

    $fp = fopen('./file/'.$res[0]['realname'].'.data', 'rb');
    $res[0]['size'] = fstat($fp)['size'];
    $size = 128; //每 128 bytes 為一個 loop
    $pos = 0; //初始化
    $buffer = "";
    while ($pos < $res[0]['size']) {
        $rbuf = fread($fp, $size + 16);

        if($pos <= $ranges[0] && $pos > $ranges[0] - $size ){
            //如果現在解密的位置在起始點前 $size 位內，則只留下 $range[0] 和 $pos 之間的內容
            $buffer = openssl_decrypt($rbuf,  'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
            echo substr($buffer, -(($pos+$size-$ranges[0]) % 128) );
        }elseif($pos >= $ranges[0] && $pos+$size < $ranges[1]){
            //如果介於起始點跟終止點之間則直接輸出內容
            $buffer = openssl_decrypt($rbuf,  'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
            echo $buffer;
        }elseif($pos < $ranges[1] && $pos+$size >= $ranges[1] ){
            //如果現在解密的位置在終止點後 $size 位內，則只留下 $pos - $size 和 $range[0] 之間的內容
            $buffer = openssl_decrypt($rbuf,  'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
            echo substr($buffer, 0, ($ranges[1] - ($pos - $size) +1) % 128 );
        }elseif($pos >= $ranges[1] && $pos >= $ranges[1] + $size){
            break;
        }
        
        $pos += $size;
        
        flush();
        @ob_flush();

        //讀取 cipher 中上一輪的最後 16 bytes 作為下一輪的 IV
        $iv = substr($rbuf, -16);
        //噴出資料
        
    }
}else{
    $fp = fopen('./file/'.$res[0]['realname'].'.data', 'rb');
    $res[0]['size'] = fstat($fp)['size'];
    $size = 128; //每 128 bytes 為一個 loop
    $pos = 0; //初始化
    $buffer = "";
    while ($pos < $res[0]['size']) {
        $rbuf = fread($fp, $size + 16);
        //解密
        $buffer = openssl_decrypt($rbuf,  'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        if ($buffer === false) {
            //echo openssl_error_string() . "\n"; exit();
        }
        echo $buffer;
        //flush();
        //@ob_flush();

        //讀取 cipher 中上一輪的最後 16 bytes 作為下一輪的 IV
        $iv = substr($rbuf, -16);
        $pos += $size;
    }
}

