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
function delete_dir($id)
{
    $result = true;
    $dir = $GLOBALS['db']->select('file', array('owner' => $_SESSION['username'], 'dir' => $id));
    if(is_array($dir) && !empty($dir)){
        foreach ($dir as $k) {
            $result = $GLOBALS['db']->delete('file', array('id' => $k['id']));
            $result = @unlink('file/'.$k['realname'].'.data');
        }
    }
    $result = $GLOBALS['db']->delete('dir', array('id' => $id));

    return $result;
}
function scan_dir($id)
{
    $result = true;
    $dir = $GLOBALS['db']->select('dir', array('owner' => $_SESSION['username'], 'parent' => $id));
    if(is_array($dir) && !empty($dir)){
        foreach ($dir as $d) {
            $result = scan_dir($d['id']);
        }
    }
    $result = delete_dir($id);

    return $result;
}
$res = $GLOBALS['db']->select('dir', array('id' => $_GET['id']));
if ($_SESSION['login'] && $_SESSION['username'] == $res[0]['owner']) {
    $result = scan_dir($_GET['id']);
    echo json_encode(array(
        'success' => $result,
        'message' => $result ? '成功刪除。' : '刪除失敗。',
    ));
    $token = @touch(dirname(dirname(__FILE__)).'/updatetoken/'.md5($_SESSION['username']).'.token');
} else {
    echo json_encode(array(
        'success' => false,
        'message' => '你不是資料夾的擁有者。',
    ));
}
