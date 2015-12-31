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

if ($_GET['type'] == 'dir') {
    $res = $db->select('dir', array('id' => $_GET['id']));
} elseif ($_GET['type'] == 'file') {
    $res = $db->select('file', array('id' => $_GET['id']));
}
if ($_SESSION['login'] && $_SESSION['username'] == $res[0]['owner']) {
    if ($_GET['type'] == 'dir') {
        $result = $db->update('dir', array('color' => $_GET['color']), array('id' => $_GET['id']));
    } elseif ($_GET['type'] == 'file') {
        $result = $db->update('file', array('color' => $_GET['color']), array('id' => $_GET['id']));
    }
    echo json_encode(array(
        'success' => $result,
        'message' => $result ? '成功標記檔案。' : '標記檔案失敗。',
    ));
    $token = @touch(dirname(dirname(__FILE__)).'/updatetoken/'.md5($_SESSION['username']).'.token');
} else {
    echo json_encode(array(
        'success' => false,
        'message' => '你不是檔案的擁有者。',
    ));
}
