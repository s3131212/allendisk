<?php
include('config.php'); 
if(!session_id()) session_start();
function delete_dir($location){
    $result = true;
    foreach ($GLOBALS['db']->select('dir',array('parent' => $location,'owner'=>$_SESSION["username"])) as $d) {
        $sdir = $GLOBALS['db']->select('dir',array('parent' => $d['id'],'owner'=>$_SESSION["username"]));
        if($sdir[0]["id"]!=null){
            $result = delete_dir($loc);
        }
        foreach ($GLOBALS['db']->select("file",array('owner'=>$_SESSION["username"],'dir' => $d["id"])) as $k) {
            $GLOBALS['db']->delete('file',array('id' => $k["id"]));
            $result = @unlink("file/" . $k["realname"] . ".data");
        }
        $GLOBALS['db']->delete('dir',array('id' => $d["id"]));
    }
    return $result;
}
function scan_dir($id){
    $result = true;
    $res = $GLOBALS['db']->select('dir',array('id' => $id));
    $sdir = $GLOBALS['db']->select('dir',array('parent' => $id,'owner'=>$_SESSION["username"]));
    if($sdir[0]["id"]!=null){
        $result = delete_dir($id);
    }
    foreach ($GLOBALS['db']->select("file",array('owner'=>$_SESSION["username"],'dir' => $id)) as $k) {
        $GLOBALS['db']->delete('file',array('id' => $k["id"]));
        $result = @unlink("file/" . $k["realname"] . ".data");
    }
    $GLOBALS['db']->delete('dir',array('id' => $id));
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