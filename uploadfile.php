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
function sizecount($size)
{
    if ($size < 0.001) {
        return round(($size * 1000 * 1000), 2).'B';
    } elseif ($size >= 0.001 && $size < 1) {
        return round(($size * 1000), 2).'KB';
    } elseif ($size >= 1 && $size < 1000) {
        return round($size, 2).'MB';
    } elseif ($size >= 1000) {
        return round(($size / 1000), 2).'GB';
    }
}
if (!isset($_FILES['file'])) {
    exit();
}

$result = '';

if ($_FILES['file']['error'] > 0) {
    $result = 'unknow';
    if ($_FILES['file']['error'] == 1 || $_FILES['file']['error'] == 2) {
        $result = 'inierr';
    }
    if ($_FILES['file']['error'] == 3) {
        $result = 'par';
    }
    if ($_FILES['file']['error'] == 4) {
        $result = 'nofile';
    }
}
if ($_FILES['file']['name'] == null) {
    $result = 'nofile';
}
if ($config['size'] != 0) {
    if ($_FILES['file']['size'] > ($config['size'] * 1000 * 1000)) {
        $result = 'sizeout';
    }
}
if ($config['total'] != 0) {
    $used = $db->ExecuteSQL(sprintf('SELECT SUM(`size`) AS `sum` FROM `file` WHERE `owner` = \'%s\' AND `recycle` = \'0\'', $db->SecureData($_SESSION['username'])));
    if ($used[0]['sum'] >= ($config['total'] * 1000 * 1000)) {
        $result = 'totalout';
    }
}

$mkid = 0;
$filename = sha1(md5(mt_rand().uniqid()));
if ($result == '') {
    move_uploaded_file($_FILES['file']['tmp_name'], './file/'.$filename.'.temp');
    $mkid = sha1(mt_rand().uniqid());
    $db->insert(array('name' => $_FILES['file']['name'], 'size' => $_FILES['file']['size'], 'owner' => $_SESSION['username'], 'id' => $mkid, 'realname' => $filename, 'secret' => '', 'type' => $_FILES['file']['type'], 'dir' => $_SESSION['dir'], 'recycle' => '0'), 'file');
    $result = 'success';
}

$return = array(
    'result' => $result,
    'id' => $mkid,
    'name' => $_FILES['file']['name'],
    'size' => sizecount($_FILES['file']['size'] / 1000 / 1000)
);

echo json_encode($return);

header('Connection: close'); //解決Upload Error Code 3