<?php
/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/

@set_time_limit(20);
if(!session_id()) session_start();
function fileformat($type,$name){
    if(preg_match("/image\/(.*)/i", $type)){
        echo strtoupper(str_replace("image/", "", $type)).' 圖檔';
    }elseif(preg_match("/audio\/(.*)/i", $type)){
        echo strtoupper(str_replace("audio/", "", $type)).' 音樂檔';
    }elseif(preg_match("/video\/(.*)/i", $type)){
        echo strtoupper(str_replace("vedio/", "", $type)).' 影片檔';
    }elseif(preg_match("/text\/(.*)/i", $type)){
        echo '純文字檔';
    }elseif($type == "application/msword" || $type == "application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
        echo 'MS Office Word';
    }elseif($type == "application/vnd.ms-excel" || $type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
        echo 'MS Office Excel';
    }elseif($type == "application/vnd.ms-powerpoint" || $type == "application/vnd.openxmlformats-officedocument.presentationml.presentation"){
        echo 'MS Office Powerpoint';
    }elseif($type == "application/x-bzip2" ||$type == "application/x-gzip" ||$type == "application/x-7z-compressed" ||$type == "application/x-rar-compressed" ||$type == "application/zip" ||$type == "application/x-apple-diskimage" ||$type == "application/x-tar"){
        echo '壓縮檔';
    }else{
        echo strtoupper(substr($name,-(strlen($name)-strrpos($name, ".")-1))).'檔';
    }
}
include(dirname(dirname(__FILE__)).'/config.php');
$res = $db->select("file",array('owner' => $_SESSION["username"],'recycle'=>'1'));
if($res[0]['id'] != null){
    foreach($res as $d){
        if($d["dir"]!=0){
            $ordir = $db->select('dir',array('id'=>$d["dir"]));
            if($ordir[0]["recycle"]==1) continue;
                $ordir = $ordir[0]["name"];
            }else{
                $ordir = "主目錄";
            }
?>
        <tr>
            <td><?php echo $d["name"]; ?></td>
            <td><?php echo fileformat($d["type"], $d['name']); ?></td>
            <td><?php echo $ordir; ?></td>
            <td>
                <div class="btn-group">
                    <a href="#"  data-id="<?php echo $d["id"] ?>" data-type="file" class="btn btn-default recycle_back">還原</a>
                    <a href="#"  data-id="<?php echo $d["id"] ?>" data-type="file" class="btn btn-danger real_delete">永久刪除</a>
                </div>
            </td>
        </tr>
<?php }
}
$res = $db->select("dir",array('owner' => $_SESSION["username"],'recycle'=>'1'));
if($res[0]['id'] != null){
    foreach($res as $d){
        if($d["parent"]!=0){
            $ordir = $db->select('dir',array('id'=>$d["parent"]));
            if($ordir[0]["recycle"]==1) continue;
            $ordir = $ordir[0]["name"];
        }else{
            $ordir = "主目錄";
        }
?>
        <tr>
            <td><?php echo $d["name"]; ?></td>
            <td>資料夾</td>
            <td><?php echo $ordir; ?></td>
            <td>
                <div class="btn-group">
                    <a href="#"  data-id="<?php echo $d["id"] ?>" data-type="dir" class="btn btn-default recycle_back">還原</a>
                    <a href="#"  data-id="<?php echo $d["id"] ?>" data-type="dir" class="btn btn-danger real_delete">永久刪除</a>
                </div>
            </td>
        </tr>
<?php }
}
exit();