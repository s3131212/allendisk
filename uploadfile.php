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
set_time_limit(0);
function sizecount($size) {

    if ($size < 0.001) {
        echo round(($size * 1000 * 1000), 2) . "B";
    } elseif ($size >= 0.001 && $size < 1) {
        echo round(($size * 1000), 2) . "KB";
    } elseif ($size >= 1 && $size < 1000) {
        echo round($size, 2) . "MB";
    } elseif ($size >= 1000) {
        echo round(($size / 1000), 2) . 'GB';
    }
}

?>
    <?php

for ($j = 0; $j < count($_FILES["file"]["name"]); ++$j) {
    $result = '';

    if ($_FILES['file']['error'][$j] > 0) {
        $result = "unknow";

        if ($_FILES['file']['error'][$j] == 1 || $_FILES['file']['error'][$j] == 2) {
            $result = "inierr";
        }

        if ($_FILES['file']['error'][$j] == 3) {
            $result = "par";
        }

        if ($_FILES['file']['error'][$j] == 4) {
            $result = "nofile";
        }
    }

    if ($_FILES['file']['name'][$j] == null) {
        $result = "nofile";
    }

    if ($config['size'] != 0) {
        if ($_FILES['file']['size'][$j] > ($config["size"] * 1000 * 1000)) {
            $result = "sizeout";
        }
    }

    if ($config['total'] != 0) {
        $used = $db
            ->ExecuteSQL(sprintf('SELECT SUM(`size`) AS `sum` FROM `file` WHERE `owner` = \'%s\' AND `recycle` = \'0\'', $db->SecureData($_SESSION["username"])));

        if ($used[0]['sum'] >= ($config["total"] * 1000 * 1000)) {
            $result = "totalout";
        }
    }

    /* Create Key */

    /*
    $passphrase['a'] 是檔案加密用的 Key
    $passphrase['b'] 是位檔案加密的 Key 作加密所使用的密碼，來自使用者的密碼
    $passphrase['c'] 由 a, b 算出，儲存在資料庫
     */
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
        $key = substr(md5("\x2D\xFC\xD8" . $passphrase['a'], true) . md5("\x2D\xFC\xD9" . $passphrase['a'], true), 0, 24);
        $opts = [
            'iv'  => $iv,
            'key' => $key
        ];
        $fp = fopen($_FILES['file']['tmp_name'][$j], 'rb');
        $dest = fopen('./file/' . $filename . '.data', 'wb');
        stream_filter_append($dest, 'mcrypt.rijndael-256', STREAM_FILTER_WRITE, $opts);
        stream_copy_to_stream($fp, $dest);
        fclose($fp);
        fclose($dest);
        $mkid = sha1(mt_rand() . uniqid());
        $db->insert([
            "name"     => $_FILES['file']['name'][$j],
            "size"     => $_FILES['file']['size'][$j],
            "owner"    => $_SESSION["username"],
            "id"       => $mkid,
            "realname" => $filename,
            'secret'   => $passphrase['c'],
            "type"     => $_FILES['file']['type'][$j],
            "dir"      => $_SESSION["dir"],
            "recycle"  => '0'
        ], "file");
        $result = "success";
    }

    $token = fopen(dirname(__FILE__) . "/updatetoken/" . md5($_SESSION['username']) . '.token', "w");
    fclose($token);

    if ($result == "success") {?>
        <tr>
            <td>
                <?php echo $_FILES['file']['name'][$j];?>
            </td>
            <td>
                <?php sizecount($_FILES['file']['size'][$j] / 1000 / 1000);?>
            </td>
            <td>上傳成功</td>
        </tr>
        <?php } else { ?>
            <tr class="error">
                <td>Unknow</td>
                <td>Unknow</td>
                <td>
                    <?php
                    switch ($result) {
                        case 'inierr':
                        case 'sizeout':
                            echo "檔案大小超出限制";
                            break;

                        case 'totalout':
                            echo "帳戶空間不足";
                            break;

                        case 'par':
                            echo "檔案上傳不完全（系統錯誤）";
                            break;

                        case 'nofile':
                            echo "沒有選取的檔案";
                            break;

                        default:
                            echo "發生未知錯誤";
                            break;
                    }
                    ?>
                </td>
            </tr>
            <?php
    }
}

header("Connection: close");
exit;
// 解決 Upload Error Code 3