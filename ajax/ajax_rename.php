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

$res = $db->select('file', array('id' => $_GET['id']));

if ($_SESSION['login'] && $_SESSION['username'] == $res[0]['owner']) {
    $result = $db->update('file', array('name' => $_GET['name']), array('id' => $_GET['id']));

    echo json_encode(array(
        'success' => $result,
        'message' => $result ? '成功重新命名檔案。' : '重新命名檔案失敗。',
    ));
    $token = @touch(dirname(dirname(__FILE__)).'/updatetoken/'.md5($_SESSION['username']).'.token');
} else {
    echo json_encode(array(
        'success' => false,
        'message' => '你不是檔案的擁有者。',
    ));
}
