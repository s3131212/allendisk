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

if ($_SESSION['login']) {
    if(!isset($_POST['name']) || $_POST['name'] == null){
        echo json_encode(array(
            'success' => false,
            'message' => "請輸入資料夾名稱",
        ));
        exit();
    }
    $name = str_replace('/', '', $_POST['name']);
    $dircheck = $db->select('dir', array('owner' => $_SESSION['username'], 'name' => $name, 'parent' => $_SESSION['dir']));
    if ($dircheck[0]['id'] != null) {
        echo json_encode(array(
            'success' => false,
            'message' => "已經有同樣的資料夾名稱",
        ));
    } else {
        $db->insert(array('id' => sha1(md5(mt_rand().uniqid())), 'name' => $name, 'owner' => $_SESSION['username'], 'parent' => $_SESSION['dir'], 'recycle' => '0'), 'dir');
        $re = 2;
        $token = @touch(dirname(dirname(__FILE__)).'/updatetoken/'.md5($_SESSION['username']).'.token');
        echo json_encode(array(
            'success' => true,
            'message' => '建立成功',
        ));
    }
}