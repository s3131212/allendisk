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
$res = $db->select('dir', array('id' => $_GET['id']));

if ($_SESSION['login'] && $_SESSION['username'] == $res[0]['owner']) {
    $result = $db->update('dir', array('name' => $_GET['name']), array('id' => $_GET['id']));
    echo json_encode(array(
        'success' => $result,
        'message' => $result ? '成功重新命名資料夾。' : '重新命名失敗。',
    ));
    $token = @touch(dirname(dirname(__FILE__)).'/updatetoken/'.md5($_SESSION['username']).'.token');
} else {
    echo json_encode(array(
        'success' => false,
        'message' => '你不是資料夾的擁有者。',
    ));
}
