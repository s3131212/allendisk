<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
include 'config.php';

if (!session_id()) {
    session_start();
}

if (!$_SESSION["login"]) {
    exit;
}

ignore_user_abort(true);
//set_time_limit(0);
function sizecount($size) {

    if ($size < 0.001) {
        return round(($size * 1000 * 1000), 2) . "B";
    } elseif ($size >= 0.001 && $size < 1) {
        return round(($size * 1000), 2) . "KB";
    } elseif ($size >= 1 && $size < 1000) {
        return round($size, 2) . "MB";
    } elseif ($size >= 1000) {
        return round(($size / 1000), 2) . 'GB';
    }
}

function getname($url) {

    if (stripos(urldecode(basename($_POST["file"])), "?") === false) {
        return urldecode(basename($_POST["file"]));
    } else {
        $string = quotemeta("/" . urldecode(basename($url)));

        if (preg_match_all("/\/(.+)\?/i", $string, $matches)) {
            return stripslashes($matches[1][0]);
        }
    }
}

if (isset($_POST["file"])) {
    $result = '';

    if ($config['total'] != 0) {
        $used = $db
            ->ExecuteSQL(sprintf('SELECT SUM(`size`) AS `sum` FROM `file` WHERE `owner` = \'%s\' AND `recycle` = \'0\'', $db->SecureData($_SESSION["username"])));

        if ($used[0]['sum'] >= ($config["total"] * 1000 * 1000)) {
            $result = "totalout";
        }
    }

    $file = @file_get_contents($_POST["file"]);
    $header = @get_headers($_POST["file"], 1);

    if ($file !== false && stripos($header[0], '200') !== false) {
        $name = getname($_POST["file"]);

        if (strlen($file) == 0) {
            // 只有當無法正常偵測大小時才使用 header ，因為header可能被偽造
            $size = $header["Content-Length"];
        } else {
            $size = strlen($file);
        }

        if ($config['size'] != 0) {
            if ($size > ($config["size"] * 1000 * 1000)) {
                $result = "sizeout";
            }
        }

        /* Create Key */
        $passphrase['a'] = sha1(md5(mt_rand() . uniqid()));
        $passphrase['b'] = $_SESSION['password'];
        $iv = substr(md5("\x1B\x3C\x58" . $passphrase['b'], true), 0, 8);
        $key = substr(md5("\x2D\xFC\xD8" . $passphrase['b'], true) . md5("\x2D\xFC\xD9" . $passphrase['b'], true), 0, 24);
        $passphrase['c'] = rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $passphrase['a'], MCRYPT_MODE_CBC, $iv)), "\0\3");
        unset($key);
        unset($iv);

        $filename = sha1(md5(mt_rand() . uniqid()));

        if ($result == '') {
            $iv = substr(md5("\x1B\x3C\x58" . $passphrase['a'], true), 0, 8);
            $key = substr(md5("\x2D\xFC\xD8" . $passphrase['a'], true) . md5("\x2D\xFC\xD9" . $passphrase, true), 0, 24);
            $opts = [
                'iv'  => $iv,
                'key' => $key
            ];
            $fp = fopen($_POST["file"], 'rb');
            //$fp = $file;
            $dest = fopen('./file/' . $filename . '.data', 'wb');
            stream_filter_append($dest, 'mcrypt.rijndael-256', STREAM_FILTER_WRITE, $opts);
            stream_copy_to_stream($fp, $dest);
            fclose($fp);
            fclose($dest);
            $mkid = sha1(mt_rand() . uniqid());
            $db->insert([
                "name"     => $name,
                "size"     => $size,
                "owner"    => $_SESSION["username"],
                "id"       => $mkid,
                'secret'   => $passphrase['c'],
                "realname" => $filename,
                "type"     => $header["Content-Type"],
                "dir"      => $_SESSION["dir"],
                "recycle"  => "0"
            ], "file");
            $result = "success";
        }
    } else {
        $result = "nofile";
    }

    if ($result == "success") {
        $data = "<tr><td>" . $name . "</td><td>" . sizecount($size / 1000 / 1000) . "</td><td>上傳成功</td></tr>";
        $token = fopen(dirname(__FILE__) . '/updatetoken/' . md5($_SESSION['username']) . '.token', "w");
        fclose($token);
    } else {

        if ($result == "sizeout") {
            $sta = "檔案太大";
        } elseif ($result == "totalout") {
            $sta = "帳戶空間不足";
        } elseif ($result == "nofile") {
            $sta = "沒有選取的檔案";
        } else {
            $sta = "發生未知得錯誤";
        }

        $data = "<tr><td>Unknow</td><td>Unknow</td><td>" . $sta . "</td></tr>";
    }
}

?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8" />
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </head>

    <body>
        <form action="remotedownload.php" method="post">
            <input id="file" name="file" type="text" required class="form-control" placeholder="請輸入網址">
            </br>
            <input id="submit" name="submit" type="submit" class="btn btn-primary" value="開始上傳">
        </form>
        <br />
        <table class="table">
            <tr>
                <td>檔案名稱</td>
                <td>檔案大小</td>
                <td>上傳結果</td>
            </tr>
            <?php

if (isset($_POST["file"])) {
    echo $data;
}

?>
        </table>
    </body>

    </html>
