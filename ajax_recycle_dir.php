<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include('config.php'); 
if(!session_id()) session_start();
function delete_dir($id){
    $result = true;
    foreach ($GLOBALS['db']->select("file",array('owner'=>$_SESSION["username"],'dir' => $id)) as $k) {
        $result = $GLOBALS['db']->update('file',array('recycle' => '1'), array('id' => $k['id']));
    }
    $result = $GLOBALS['db']->update('dir',array('recycle' => '1'), array('id' => $id));
    return $result;
}
function scan_dir($id){
    $result = true;
    foreach ($GLOBALS['db']->select("dir",array('owner'=>$_SESSION["username"],'parent' => $id)) as $d) {
        $result = scan_dir($d["id"]);
    }
    $result = delete_dir($id);
    return $result;
}
$res = $GLOBALS['db']->select('dir',array('id' => $_GET["id"]));
if($_SESSION["login"] && $_SESSION["username"] == $res[0]["owner"]){
    $result = scan_dir($_GET['id']);
    echo json_encode(array(
        "success" => $result,
        "message" => $result ? "成功刪除。" : "刪除失敗。"
    ));
}
else {
    echo json_encode(array(
        "success" => false,
        "message" => "你不是資料夾的擁有者。"
    ));
}