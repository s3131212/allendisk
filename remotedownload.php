<?php
/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include 'config.php';
require_once('class/safecurl/autoload.php');
use fin1te\SafeCurl\SafeCurl;
use fin1te\SafeCurl\Exception; 

/* Start to develop here. Best regards https://php-download.com/ */

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
        return round(($size * 1000 * 1000), 2).'B';
    } elseif ($size >= 0.001 && $size < 1) {
        return round(($size * 1000), 2).'KB';
    } elseif ($size >= 1 && $size < 1000) {
        return round($size, 2).'MB';
    } elseif ($size >= 1000) {
        return round(($size / 1000), 2).'GB';
    }
}
function getname($url)
{
    if (stripos(urldecode(basename($_POST['file'])), '?') === false) {
        return urldecode(basename($_POST['file']));
    } else {
        $string = quotemeta('/'.urldecode(basename($url)));
        if (preg_match_all("/\/(.+)\?/i", $string, $matches)) {
            return stripslashes($matches[1][0]);
        }
    }
}

if (isset($_POST['file'])) {
    $result = '';
    if ($config['total'] != 0) {
        $used = $db->ExecuteSQL(sprintf('SELECT SUM(`size`) AS `sum` FROM `file` WHERE `owner` = \'%s\' AND `recycle` = \'0\'', $db->SecureData($_SESSION['username'])));
        if ($used[0]['sum'] >= ($config['total'] * 1000 * 1000)) {
            $result = 'totalout';
        }
    }
    try {
        $curlHandle = curl_init();
        $file = SafeCurl::execute($_POST['file'], $curlHandle);
    } catch (Exception $e) {
        $return = array(
            'result' => 'nofile',
            'id' => '0',
            'name' => 'null',
            'size' => 0
        );

        echo json_encode($return);
        exit();
    }
    $header = @get_headers($_POST['file'], 1);
    $name = getname($_POST['file']);
    if (strlen($file) == 0) { // 只有當無法正常偵測大小時才使用 header ，因為header可能被偽造
        $size = $header['Content-Length'];
    } else {
        $size = strlen($file);
    }
    if ($config['size'] != 0) {
        if ($size > ($config['size'] * 1000 * 1000)) {
            $result = 'sizeout';
        }
    }

    $filename = sha1(md5(mt_rand().uniqid()));
    if ($result == '') {
        $fp = fopen($_POST['file'], 'rb');
        file_put_contents('./file/'.$filename.'.temp', file_get_contents($_POST['file']));
        $mkid = sha1(mt_rand().uniqid());
        $db->insert(array('name' => $name, 'size' => $size, 'owner' => $_SESSION['username'], 'id' => $mkid, 'secret' => '', 'realname' => $filename, 'type' => $header['Content-Type'], 'dir' => $_SESSION['dir'], 'recycle' => '0'), 'file');
        $result = 'success';
    }
    $return = array(
        'result' => $result,
        'id' => $mkid,
        'name' => $name,
        'size' => sizecount($size / 1000 / 1000)
    );

    echo json_encode($return);
}