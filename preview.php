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
    $iv = md5("\x1B\x3C\x58".$passphrase, true).md5("\x1B\x3C\x58".$passphrase, true);
    $key = substr(md5("\x2D\xFC\xD8".$passphrase, true).md5("\x2D\xFC\xD9".$passphrase, true), 0, 24);
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
            $key = substr(md5("\x2D\xFC\xD8".$passphrase, true).md5("\x2D\xFC\xD9".$passphrase, true), 0, 24);
            $opts = array('iv' => $iv, 'key' => $key);

            $fp_filter = stream_filter_append($fp, 'mdecrypt.rijndael-256', STREAM_FILTER_READ, $opts);
        }
        $buffer .= stream_get_contents($fp, $size, $pos);
        $pos += $size;
    }
    return $buffer;
}
function decrypt_code($code)
{
    if($code == '') return 'nopassword';
    $passphrase['b'] = $_SESSION['password'];
    $passphrase['c'] = $code;
    $iv = md5("\x1B\x3C\x58".$passphrase['b'], true).md5("\x1B\x3C\x58".$passphrase['b'], true);
    $key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 24);

    return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($passphrase['c']), MCRYPT_MODE_CBC, $iv), "\0\3");
}
if (isset($_GET['id']) && $_GET['id'] != null) {
    $res = $db->select('file', array('id' => $_GET['id']));
    if ($res[0]['owner'] != $_SESSION['username']) {
        header('Location:index.php');
        exit();
    }
    if (preg_match("/image\/(.*)/i", $res[0]['type'])) {
        echo '<img src="readfile.php?id='.$res[0]['id'].'&password='.decrypt_code($res[0]['secret']).'" style="max-width:90%; max-height:90%; width:auto; height:auto;" />';
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
            <source src="readfile.php?id='.$res[0]['id'].'&password='.decrypt_code($res[0]['secret']).'" />
        </audio>';
    } elseif (preg_match("/video\/(.*)/i", $res[0]['type'])) {
        echo '<video style="width:100%;height:90%;" controls><source src="readfile.php?id='.$res[0]['id'].'&password='.decrypt_code($res[0]['secret']).'" type="'.$res[0]['type'].'">很抱歉，您的瀏覽器暫時無法預覽影片</video>';
    } elseif ($res[0]['type'] == 'text/html') {
        header('Content-Type: text/html');
        echo decode_file($_GET['id']);
    } elseif (preg_match("/text\/(.*)/i", $res[0]['type'])) {
        header('Content-Type: text/html');
        header('Content-Disposition: inline; filename="'.$res[0]['name'].'"');
        echo htmlspecialchars((decode_file($_GET['id'])));
    } elseif ($res[0]['type'] == 'application/msword' || $res[0]['type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $res[0]['type'] == 'application/vnd.ms-excel' || $res[0]['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $res[0]['type'] == 'application/vnd.ms-powerpoint' || $res[0]['type'] == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
        $token = base64_encode(json_encode(array(array('id' => $_GET['id'], 'time' => time(), 'dir' => $res[0]['dir']))));
        $url = $config['url'].'readfile.php?id='.$_GET['id'].'&pretoken='.$token.'&password='.decrypt_code($res[0]['secret']);
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
