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

$res = $db->select('file', array('id' => $_GET['id']));

if ($_SESSION['login'] && $_SESSION['username'] == $res[0]['owner']) {
    $db->delete('file', array('id' => $_GET['id']));

    $result = @unlink(dirname(dirname(__FILE__)).'/file/'.$res[0]['realname'].'.data');

    echo json_encode(array(
        'success' => $result,
        'message' => $result ? '成功刪除檔案。' : '刪除檔案失敗。',
    ));
    $token = @touch(dirname(dirname(__FILE__)).'/updatetoken/'.md5($_SESSION['username']).'.token');
} else {
    echo json_encode(array(
        'success' => false,
        'message' => '你不是檔案的擁有者。',
    ));
}
