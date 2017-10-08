<?php

/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
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

    $dir = $GLOBALS['db']->select('dir', array('parent' => $id));
    $file = $GLOBALS['db']->select('file', array('dir' => $id));
    if(is_array($dir) && !empty($dir)){
        foreach ($dir as $k) {
            $result = $GLOBALS['db']->update('dir', array('share' => $per), array('id' => $k['id']));
            $result = scan_dir($k['id'], $per);
        }
    }
    if(is_array($file) && !empty($file)){
        foreach ($file as $d) {
            $result = $GLOBALS['db']->update('file', array('share' => $per), array('id' => $d['id']));
        }
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
        'message' => ($result !== false) ? '成功修改檔案權限。' : '修改檔案權限失敗。',
    ));
    $token = @touch(dirname(dirname(__FILE__)).'/updatetoken/'.md5($_SESSION['username']).'.token');
} else {
    echo json_encode(array(
        'success' => false,
        'message' => '你不是檔案的擁有者。',
    ));
}
