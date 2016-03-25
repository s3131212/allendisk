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
if (!$_SESSION['login']) {
    exit();
}
@ignore_user_abort(true);
@set_time_limit(0);
function sizecount($size)
{
    if ($size < 0.001) {
        echo round(($size * 1000 * 1000), 2).'B';
    } elseif ($size >= 0.001 && $size < 1) {
        echo round(($size * 1000), 2).'KB';
    } elseif ($size >= 1 && $size < 1000) {
        echo round($size, 2).'MB';
    } elseif ($size >= 1000) {
        echo round(($size / 1000), 2).'GB';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
<table class="table">
    <tr>
        <td>檔案名稱</td>
        <td>檔案大小</td>
        <td>上傳結果</td>
    </tr>
<?php
if (!isset($_FILES['file'])) {
    exit();
}
for ($j = 0; $j < count($_FILES['file']['name']); ++$j) {
    $result = '';
    if ($_FILES['file']['error'][$j] > 0) {
        $result = 'unknow';
        if ($_FILES['file']['error'][$j] == 1 || $_FILES['file']['error'][$j] == 2) {
            $result = 'inierr';
        }
        if ($_FILES['file']['error'][$j] == 3) {
            $result = 'par';
        }
        if ($_FILES['file']['error'][$j] == 4) {
            $result = 'nofile';
        }
    }
    if ($_FILES['file']['name'][$j] == null) {
        $result = 'nofile';
    }
    if ($config['size'] != 0) {
        if ($_FILES['file']['size'][$j] > ($config['size'] * 1000 * 1000)) {
            $result = 'sizeout';
        }
    }
    if ($config['total'] != 0) {
        $used = $db->ExecuteSQL(sprintf('SELECT SUM(`size`) AS `sum` FROM `file` WHERE `owner` = \'%s\' AND `recycle` = \'0\'', $db->SecureData($_SESSION['username'])));
        if ($used[0]['sum'] >= ($config['total'] * 1000 * 1000)) {
            $result = 'totalout';
        }
    }

    $filename = sha1(md5(mt_rand().uniqid()));
    if ($result == '') {
        if($config['encrypt_file']){
            /* Create Key */
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
            $fp = fopen($_FILES['file']['tmp_name'][$j], 'rb');
            $dest = fopen('./file/'.$filename.'.data', 'wb');
            stream_filter_append($dest, 'mcrypt.rijndael-256', STREAM_FILTER_WRITE, $opts);
            stream_copy_to_stream($fp, $dest);
            fclose($fp);
            fclose($dest);
        }else{
            move_uploaded_file($_FILES['file']['tmp_name'], './file/'.$filename.'.data');
            $passphrase['c'] = null;
        }
        $mkid = sha1(mt_rand().uniqid());
        $db->insert(array('name' => $_FILES['file']['name'][$j], 'size' => $_FILES['file']['size'][$j], 'owner' => $_SESSION['username'], 'id' => $mkid, 'realname' => $filename, 'secret' => $passphrase['c'], 'type' => $_FILES['file']['type'][$j], 'dir' => $_SESSION['dir'], 'recycle' => '0'), 'file');
        $result = 'success';
    }
    $token = @touch(dirname(__FILE__).'/updatetoken/'.md5($_SESSION['username']).'.token');
    if ($result == 'success') {
        ?>
        <tr>
            <td><?php echo $_FILES['file']['name'][$j];
        ?></td>
            <td><?php sizecount($_FILES['file']['size'][$j] / 1000 / 1000);
        ?></td>
            <td>上傳成功</td>
        </tr>
    <?php 
    } else {
        ?>
        <tr class="error">
            <td>Unknow</td>
            <td>Unknow</td>
            <td><?php if ($result == 'sizeout') {
    echo '檔案太大';
} elseif ($result == 'unknow') {
    echo '找不到該檔案，或是發生未知得錯誤';
} elseif ($result == 'totalout') {
    echo '帳戶空間不足';
} elseif ($result == 'inierr') {
    echo '檔案超過 POST 或是伺服器設定限制';
} elseif ($result == 'par') {
    echo '系統錯誤，檔案上傳不完全';
} elseif ($result == 'nofile') {
    echo '沒有選取的檔案';
} else {
    echo '發生未知得錯誤';
}
    }
    ?></td>
        </tr>
<?php 
}
header('Connection: close'); //解決Upload Error Code 3
 ?>

</table>
<a href="uploadiframe.php" class="btn btn-primary">上傳更多</a>
</body>
</html>