<?php

/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include dirname(dirname(__FILE__)).'/config.php';
if (!session_id()) {
    session_start();
}
function scan_dir($id, $per)
{
    $result = true;
    foreach ($GLOBALS['db']->select('dir', array('parent' => $id)) as $k) {
        $result = $GLOBALS['db']->update('dir', array('share' => $per), array('id' => $k['id']));
        $result = scan_dir($k['id'], $per);
    }
    foreach ($GLOBALS['db']->select('file', array('dir' => $id)) as $d) {
        $result = $GLOBALS['db']->update('file', array('share' => $per), array('id' => $d['id']));
    }

    return $result;
}
$res = $db->select($_GET['type'], array('id' => $_GET['id'], 'recycle' => '0'));
if ($_SESSION['login'] && $_SESSION['username'] == $res[0]['owner']) {
    if ($res[0]['share'] == '0') {
        $per = '1';
    } else {
        $per = '0';
    }
    if ($_GET['type'] == 'dir') {
        $db->update('dir', array('share' => $per), array('id' => $_GET['id']));
        $result = scan_dir($_GET['id'], $per);
    } else {
        $result = $db->update('file', array('share' => $per), array('id' => $_GET['id']));
    }
    echo json_encode(array(
        'success' => $result,
        'message' => $result ? '成功修改檔案權限。' : '修改檔案權限失敗。',
    ));
    $token = @touch(dirname(dirname(__FILE__)).'/updatetoken/'.md5($_SESSION['username']).'.token');
} else {
    echo json_encode(array(
        'success' => false,
        'message' => '你不是檔案的擁有者。',
    ));
}
