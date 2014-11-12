<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include('config.php'); 
if(!session_id()) session_start();
$res = $GLOBALS['db']->select('dir',array('id' => $_GET["id"]));
if($_SESSION["login"] && $_SESSION["username"] == $res[0]["owner"]){
    $result = $GLOBALS['db']->update('dir',array('parent' => $_GET['dir']), array('id' => $_GET['id']));
    echo json_encode(array(
        "success" => $result,
        "message" => $result ? "成功移動。" : "移動失敗。"
    ));
}
else {
    echo json_encode(array(
        "success" => false,
        "message" => "你不是資料夾的擁有者。"
    ));
}