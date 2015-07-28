<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */

require 'require.php';
header("Cache-Control: no-store, no-cache, must-revalidate");

_session_start();

function decode_file($id, $do) {
    /* Get file id */
    $res = $GLOBALS['db']->select("file", [
        "id" => $id
    ]);

    /* Get file secret */
    $passphrase = decrypt_code($res[0]['secret']);

    /* Decode */
    $iv = substr(md5("\x1B\x3C\x58" . $passphrase, true), 0, 8);
    $key = substr(md5("\x2D\xFC\xD8" . $passphrase, true) . md5("\x2D\xFC\xD9" . $passphrase, true), 0, 24);
    $opts = [
        'iv'  => $iv,
        'key' => $key
    ];
    $fp = fopen('./file/' . $res[0]["realname"] . '.data', 'rb');
    stream_filter_append($fp, 'mdecrypt.rijndael-256', STREAM_FILTER_READ, $opts);
    return stream_get_contents($fp);
}

function decrypt_code($code) {
    $passphrase['b'] = $_SESSION['password'];
    $passphrase['c'] = $code;
    $iv = substr(md5("\x1B\x3C\x58" . $passphrase['b'], true), 0, 8);
    $key = substr(md5("\x2D\xFC\xD8" . $passphrase['b'], true) . md5("\x2D\xFC\xD9" . $passphrase['b'], true), 0, 24);
    $b64dcod = base64_decode($passphrase['c']);
    $res = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $b64dcod, MCRYPT_MODE_CBC, $iv);
    return rtrim($res, "\0\3");
}

if (isset($_GET["id"]) && $_GET["id"] != null) {
    $id = $_GET["id"];

    /* Authorize file owner */
    $res = $GLOBALS['db']->select("file", [
        "id" => $id
    ]);
    $res = $res[0];
    if ($res["owner"] != $_SESSION["username"]) {
        header("Location: index.php");
        exit;
    }

    /* File types -> Different ways to show file */
    $baseReadFileURL = "readfile.php?id={$res["id"]}&password={" . decrypt_code($res['secret']) . "}";
    if (preg_match("/image\/(.*)/i", $res["type"])) {
        echo "<img src='{$baseReadFileURL}' style='max-width:90%; max-height:90%; width:auto; height:auto;' />";
    } elseif (preg_match("/audio\/(.*)/i", $res["type"])) {
        ?>
        <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
        <script src="js/audio.min.js"></script>
        <script>
            $(document).ready(function(){
                audiojs.events.ready(function() {
                    var as = audiojs.createAll();
                });
            });
        </script>
        <audio preload="auto" style="width:100%;">
            <source src="<?php echo $baseReadFileURL;?>" />
        </audio>
        <?php
    } elseif (preg_match("/video\/(.*)/i", $res["type"])) {
        $url = $baseReadFileURL . " type=\"{$res["type"]}\"";
        ?>
        <video style="width:100%;height:90%;" controls>
            <source src="<?php echo $url; ?>">
            很抱歉，您的瀏覽器暫時無法預覽影片。
        </video>
        <?php
    } elseif ($res["type"] == "text/html") {
        header("Content-Type: text/html");
        echo decode_file($id);
    } elseif (preg_match("/text\/(.*)/i", $res["type"])) {
        header("Content-Type: text/html; charset=utf-8");
        echo "<pre>" . htmlentities(decode_file($id)) . "</pre>";
    } else {
        $officeAppsTypes = [ "application/msword",  "application/vnd.openxmlformats-officedocument.wordprocessingml.document",  "application/vnd.ms-excel",  "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",  "application/vnd.ms-powerpoint",  "application/vnd.openxmlformats-officedocument.presentationml.presentation" ];
        foreach ($officeAppsTypes as $key => $value) {
            if ($res["type"] == $value) {
                $token = base64_encode(json_encode([
                    [
                        "id"   => $id,
                        "time" => time(),
                        "dir"  => $res["dir"]
                    ]
                ]));
                $url = $config["url"] . "readfile.php?id=" . $id . "&pretoken=" . $token . "&password=" . decrypt_code($res['secret']);
                ?>
                <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
                        <script src="//netdna.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
                        <link href="css/bootstrap.min.css" rel="stylesheet">
                        <title>Preview document</title>
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
                     <a id="link" target="_blank" class="btn btn-primary" href="https://view.officeapps.live.com/op/view.aspx?src=<?php echo urlencode($url);?>">在新的分頁開啟</a>
                 </body>
                 </html>
                 <?php
                 exit;
            }
        }
    }
} else {
    header("Location: index.php");
    exit;
}