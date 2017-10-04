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
function check_dir($id)
{
    $dir = $GLOBALS['db']->select('dir', array('id' => $id));
    if ($dir[0]['recycle'] == '1'/* || $dir[0]['share'] == '0'*/) {
        return false;
    }
    if ($dir[0]['parent'] != '0') {
        $updir = $GLOBALS['db']->select('dir', array('id' => $dir[0]['parent']));
        if ($updir[0]['recycle'] == '1'/* || $updir[0]['share'] == '0'*/) {
            return false;
        } else {
            return check_dir($updir[0]['id']);
        }
    } else {
        return true;
    }
}
function base64url_decode($data) { 
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
}
function base64url_encode($data) { 
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
} 
function fileformat($type, $name)
{
    if (preg_match("/image\/(.*)/i", $type)) {
        echo strtoupper(str_replace('image/', '', $type)).' 圖檔';
    } elseif (preg_match("/audio\/(.*)/i", $type)) {
        echo strtoupper(str_replace('audio/', '', $type)).' 音樂檔';
    } elseif (preg_match("/video\/(.*)/i", $type)) {
        echo strtoupper(str_replace('video/', '', $type)).' 影片檔';
    } elseif (preg_match("/text\/(.*)/i", $type)) {
        echo '純文字檔';
    } elseif ($type == 'application/msword' || $type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
        echo 'MS Office Word';
    } elseif ($type == 'application/vnd.ms-excel' || $type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        echo 'MS Office Excel';
    } elseif ($type == 'application/vnd.ms-powerpoint' || $type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
        echo 'MS Office Powerpoint';
    } elseif ($type == 'application/x-bzip2' || $type == 'application/x-gzip' || $type == 'application/x-7z-compressed' || $type == 'application/x-rar-compressed' || $type == 'application/zip' || $type == 'application/x-apple-diskimage' || $type == 'application/x-tar') {
        echo '壓縮檔';
    } else {
        echo strtoupper(substr($name, -(strlen($name) - strrpos($name, '.') - 1))).'檔';
    }
}
function decode_dir_passphrase($dir){
    $user = $GLOBALS['db']->select('user', array('name' => $dir[0]['owner']));

    /* Decode Password */
    if($_GET['passphrase'] != ''){
        $passphrase['b'] = substr($dir[0]['id'], 0, 20).substr($user[0]['pass'], 0, 20);
        $passphrase['c'] = base64url_decode($_GET['passphrase']);
        $iv = substr(md5("\x1B\x3C\x58".$passphrase['b'], true).md5("\x1B\x3C\x58".$passphrase['b'], true), 0 ,16);
        $key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 32);
        $passphrase['a'] = openssl_decrypt($passphrase['c'], 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    }else{
        $passphrase['a'] = 'nopassword';
    }
    return $passphrase['a'];
}
function create_dir_passphrase($dir, $key){
    global $config;
    if(!$config['encrypt_file']) return '';

    /* Create Key */
    /*
        $passphrase['a'] 是使用者密碼
        $passphrase['b'] 是資料夾 ID 和使用者 Hash 過的密碼混和
        $passphrase['c'] 由 a, b 算出
    */
    $user = $GLOBALS['db']->select('user', array('name' => $dir['owner']));
    $passphrase['a'] = $key;
    $passphrase['b'] = substr($dir['id'], 0, 20).substr($user[0]['pass'], 0, 20);
    //print_r($passphrase);

    $iv = substr(md5("\x1B\x3C\x58".$passphrase['b'], true).md5("\x1B\x3C\x58".$passphrase['b'], true), 0 ,16);
    $key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 32);
    $passphrase['c'] = openssl_encrypt($passphrase['a'], 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    //print_r($passphrase);

    return base64url_encode($passphrase['c']);
}
function decode_file_passphrase($secret, $key){
    /* Decode Password */
    if($secret != ''){
        $passphrase['b'] = $key;
        $passphrase['c'] = base64_decode($secret);
        $iv = substr(md5("\x1B\x3C\x58".$passphrase['b'], true).md5("\x1B\x3C\x58".$passphrase['b'], true), 0 ,16);
        $key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 32);
        $passphrase['a'] = openssl_decrypt($passphrase['c'], 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    }else{
        $passphrase['a'] = 'nopassword';
    }
    return base64url_encode($passphrase['a']);
}
$dir = $db->select('dir', array('id' => $_GET['id']));
$password = decode_dir_passphrase($dir);

$dircheck = $db->select('dir', array('owner' => $dir[0]['owner'], 'parent' => $_GET['id'], 'recycle' => '0', 'share' => '1'));
$filecheck = $db->select('file', array('owner' => $dir[0]['owner'], 'dir' => $_GET['id'], 'recycle' => '0', 'share' => '1'));
if (!check_dir($_GET['id'])) {
    $alert = "<div class='alert alert-warning'>此資料夾不存在或是被設定為不公開</div>";
    $dir[0]['name'] = '空白資料夾';
} else {
    $alert = '';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/bootstrap.min.js"></script>
    <title><?php echo $dir[0]['name'] ?> - <?php echo $config['sitename'] ?></title>
</head>
<body>
    <div class="container">
        <h1 class="text-center"><?php echo $config['sitetitle']; ?></h1>
        <?php include('nav.php'); ?>
        <h2>檢視<?php echo $dir[0]['name'] ?></h2>
        <?php echo $alert; ?>
        <table class="table">
            <thead>
                <tr>
                    <td>檔名</td>
                    <td>檔案類型</td>
                    <td>動作</td>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($alert == '') {
                if ($dircheck[0]['id'] != null) {
                    foreach ($dircheck as $d) {
                        ?>
                        <tr>
                            <td><?php echo $d['name']; ?></td>
                            <td>資料夾</td>
                            <td><a href="share_dir.php?id=<?php echo $d['id']; ?>&passphrase=<?php echo create_dir_passphrase($d, $password) ?>" class="btn btn-default">開啟</a></td>
                        </tr>
            <?php 
                    }
                }
                if ($filecheck[0]['id'] != null) {
                    foreach ($filecheck as $d) {
                        ?>
                        <tr>
                            <td><?php echo $d['name']; ?></td>
                            <td><?php fileformat($d['type'], $d['name']); ?></td>
                            <td><a href="downfile.php?id=<?php echo $d['id']; ?>&password=<?php echo decode_file_passphrase($d['secret'], $password) ?>" target="_blank" class="btn btn-default">下載</a></td>
                        </tr>
            <?php 
                    }
                }
            } ?>
            </tbody>
        </table>
        <p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
    </div>
</body>
</html>
