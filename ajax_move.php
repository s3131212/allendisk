<?php
include('config.php'); 
if(!session_id()) session_start();

$res = $db->select("file",array('id' => $_GET['id']));

if($_SESSION["login"] && $_SESSION["username"] == $res[0]["owner"]){
    $result = $db->update('file',array('dir' => $_GET["dir"]), array('id' => $_GET['id']));
    
    echo json_encode(array(
        "success" => $result,
        "message" => $result ? "成功移動檔案。" : "移動失敗。"
    ));
}
else {
    echo json_encode(array(
        "success" => false,
        "message" => "你不是檔案的擁有者。"
    ));
}