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
function back_dir($id)
{
    $result = true;
    $file_list = $GLOBALS['db']->select('file', array('owner' => $_SESSION['username'], 'dir' => $id));
    if (is_array($file_list)) {
        foreach ($file_list as $k) {
            $result = $GLOBALS['db']->update('file', array('recycle' => '0'), array('id' => $k['id']));
        }
    }
    $result = $GLOBALS['db']->update('dir', array('recycle' => '0'), array('id' => $id));

    return $result;
}
function scan_dir($id)
{
    $result = true;
    $file_list = $GLOBALS['db']->select('dir', array('owner' => $_SESSION['username'], 'parent' => $id));
    if (is_array($file_list)) {
        foreach ($file_list as $d) {
            $result = scan_dir($d['id']);
        }
    }
    $result = back_dir($id);

    return $result;
}
$res = $GLOBALS['db']->select('dir', array('id' => $_GET['id']));
if ($_SESSION['login'] && $_SESSION['username'] == $res[0]['owner']) {
    $result = scan_dir($_GET['id']);
    echo json_encode(array(
        'success' => $result,
        'message' => ($result !== false) ? '成功還原' : '還原失敗。',
    ));
} else {
    echo json_encode(array(
        'success' => false,
        'message' => '你不是資料夾的擁有者。',
    ));
}
