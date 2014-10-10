<?php
include('config.php'); 
if(!session_id()) session_start();
/*
function rename_dir($id,$name){
    $res = $db->update('dir',array('name' => $_GET["name"]), array('id' => $id));
    if($res[0]["location"]=="/"){
        $loc = "/".$res[0]["name"];
        $nloc = "/".$name;
    }else{
        $loc = $res[0]["location"].$res[0]["name"];
        $nloc = $res[0]["location"].$name;
    }
    $word_count = mb_strlen($loc, "utf-8");
    foreach ($db->select("file",array('dir' => $loc.'/%'),'','',true) as $value) {
        if($value["owner"]==$_SESSION["username"]){
            if($value['dir']==$loc){
                $db->update('file',array('dir' => $nloc), array('id' => $value['id']));
            }else{
                mb_substr($value['dir'],-,$word_count+1,"utf-8");
                $db->update('file',array('dir' => $nloc), array('id' => $value['id']));
            }

        }
    }
}
*/
$res = $db->select("dir",array('id' => $_GET['id']));

if($_SESSION["login"] && $_SESSION["username"] == $res[0]["owner"]){
    $result = $db->update('dir',array('name' => $_GET["name"]), array('id' => $_GET['id']));
    echo json_encode(array(
        "success" => $result,
        "message" => $result ? "成功重新命名資料夾。" : "重新命名失敗。"
    ));
}
else {
    echo json_encode(array(
        "success" => false,
        "message" => "你不是資料夾的擁有者。"
    ));
}