<?php
include('config.php'); 
if(!session_id()) session_start();

$res = $db->select("file",array('id' => $_GET['id']));

if($_SESSION["login"] && $_SESSION["username"] == $res[0]["owner"]){
    $db->delete('file',array('id' => $_GET['id']));
    
    $result = @unlink("file/" . $res[0]["realname"] . ".data");
    
    echo json_encode(array(
        "success" => $result,
        "message" => $result ? "成功刪除檔案。" : "刪除檔案失敗。"
    ));
}
else {
    echo json_encode(array(
        "success" => false,
        "message" => "你不是檔案的擁有者。"
    ));
}