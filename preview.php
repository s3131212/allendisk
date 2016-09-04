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
header('Cache-Control: no-store, no-cache, must-revalidate');
function decode_file($id)
{  
    $res = $GLOBALS['db']->select('file', array('id' => $id));

    /* 檢查是否有密碼 */
    if($res[0]['secret'] == null){
        return file_get_contents('./file/'.$res[0]['realname'].'.data');
    }

    $passphrase = decrypt_code($res[0]['secret']);
    $fp = fopen('./file/'.$res[0]['realname'].'.data', 'rb');//打開檔案
    $fsize = fstat($fp)['size'];
    $size = 128; //每 128 bytes 為一個 loop
    $pos = 0; //初始化
    $buffer = "";
    $iv = $passphrase['iv'];
    while ($pos < $fsize) {
        $rbuf = fread($fp, $size + 16);
        //解密
        $buffer.= openssl_decrypt($rbuf,  'aes-256-cbc', $passphrase['key'], OPENSSL_RAW_DATA, $iv);
        if ($buffer === false) {
            //echo openssl_error_string() . "\n"; exit();
        }

        //讀取 cipher 中上一輪的最後 16 bytes 作為下一輪的 IV
        $iv = substr($rbuf, -16);
        //噴出資料
        $pos += $size;
    }
    return $buffer;
}
function decrypt_code($code)
{
    if($code == '') return 'nopassword';
    $passphrase['b'] = $_SESSION['password'];
    $passphrase['c'] = base64_decode($code);
    $iv = substr(md5("\x1B\x3C\x58".$passphrase['b'], true).md5("\x1B\x3C\x58".$passphrase['b'], true), 0 ,16);
    $key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 32);
    $passphrase['a'] = openssl_decrypt($passphrase['c'], 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    return array("key" => substr($passphrase['a'], 0, 32), "iv" => substr($passphrase['a'], 32, 16));
}
function base64url_encode($data) { 
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
} 
if (isset($_GET['id']) && $_GET['id'] != null) {
    $res = $db->select('file', array('id' => $_GET['id']));
    if ($res[0]['owner'] != $_SESSION['username']) {
        header('Location:index.php');
        exit();
    }
    if (preg_match("/image\/(.*)/i", $res[0]['type'])) {
        $pass = decrypt_code($res[0]['secret']);
        echo '<img src="readfile.php?id='.$res[0]['id'].'&password='.base64url_encode($pass['key'] . $pass['iv']).'" style="max-width:90%; max-height:90%; width:auto; height:auto;" />';
    } elseif (preg_match("/audio\/(.*)/i", $res[0]['type'])) {
        echo '<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
        <script src="js/audio.min.js"></script>
        <script>
            $(document).ready(function(){
                audiojs.events.ready(function() {
                    var as = audiojs.createAll();
                });
            });
        </script>
        <audio preload="auto" style="width:100%;">
            <source src="readfile.php?id='.$res[0]['id'].'&password='.base64url_encode($pass['key'] . $pass['iv']).'" />
        </audio>';
    } elseif (preg_match("/video\/(.*)/i", $res[0]['type'])) {
        $pass = decrypt_code($res[0]['secret']);
        echo '<video style="width:100%;height:90%;" controls><source src="readfile.php?id='.$res[0]['id'].'&password='.base64url_encode($pass['key'] . $pass['iv']).'&streaming=true" type="'.$res[0]['type'].'">很抱歉，您的瀏覽器暫時無法預覽影片</video>';
    } elseif ($res[0]['type'] == 'text/html') {
        header('Content-Type: text/html');
        echo decode_file($_GET['id']);
    } elseif (preg_match("/text\/(.*)/i", $res[0]['type'])) {
        header('Content-Type: text/html');
        header('Content-Disposition: inline; filename="'.$res[0]['name'].'"');
        echo htmlspecialchars((decode_file($_GET['id'])));
    } elseif ($res[0]['type'] == 'application/msword' || $res[0]['type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $res[0]['type'] == 'application/vnd.ms-excel' || $res[0]['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $res[0]['type'] == 'application/vnd.ms-powerpoint' || $res[0]['type'] == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
        $pass = decrypt_code($res[0]['secret']);
        $token = base64_encode(json_encode(array(array('id' => $_GET['id'], 'time' => time(), 'dir' => $res[0]['dir']))));
        $url = $config['url'].'readfile.php?id='.$_GET['id'].'&pretoken='.$token.'&password='.base64url_encode($pass['key'] . $pass['iv']);
        //header("Location: https://view.officeapps.live.com/op/view.aspx?src=".urlencode($url));
        echo '
        <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                
                <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
                <script src="//netdna.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
                <link href="css/bootstrap.min.css" rel="stylesheet">
                <title>Document</title>
                <script>
                    $(function(){
                        $("#link").on("click", function(){
                            parent.$("#preview-file").modal("hide");
                        });

                    });
                </script>
         </head>
         <body>
             <base target="_blank">
             <a id="link" target="_blank" class="btn btn-primary" href="https://view.officeapps.live.com/op/view.aspx?src='.urlencode($url).'">在新的分頁開啟</a>
             
         </body>
         </html>';
    }
}
