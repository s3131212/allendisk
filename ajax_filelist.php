<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include('config.php'); 
if(!session_id()) session_start();
function sizecount($size){
    if ($size<0.001) {
        echo round(($size*1000*1000), 2) . "B";
    }elseif ($size>=0.001 &&$size < 1) {
        echo round(($size*1000), 2) . "KB";
    }elseif ($size>=1 &&$size < 1000) {
        echo round($size, 2) . "MB";
    }elseif ($size >= 1000) {
        echo round(($size/1000), 2) . 'GB';
    }
}
function linkcheck($type, $id,$name){
    if(preg_match("/image\/(.*)/i", $type) || preg_match("/audio\/(.*)/i", $type)||preg_match("/video\/(.*)/i", $type) || preg_match("/text\/(.*)/i", $type) || $type == "application/pdf" || $type == "application/x-shockwave-flash" ){
        echo '<a href="readfile.php/'.$name.'?id='.$id.'" target="_blank" class="btn btn-info"  data-toggle="tooltip" data-placement="top" data-container="body" title="外連檔案"><i class="ion-link"></i></a>';
    }else{
        echo '<a href="#" class="btn btn-info disabled"><i class="ion-link"></i></a>';
    }
}

function previewcheck($type, $id){
    if(preg_match("/image\/(.*)/i", $type) || preg_match("/audio\/(.*)/i", $type)||preg_match("/video\/(.*)/i", $type) || preg_match("/text\/(.*)/i", $type) || $type == "application/msword" || $type == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" || $type == "application/vnd.ms-excel" || $type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || $type == "application/vnd.ms-powerpoint" || $type == "application/vnd.openxmlformats-officedocument.presentationml.presentation"){
        echo '<a href="#" data-preview-id="'.$id.'" class="btn btn-warning" data-toggle="tooltip" data-container="body" data-placement="top" title="預覽"><i class="ion-ios7-eye"></i></a>';
    }else{
        echo '<a href="#" class="btn btn btn-warning disabled"><i class="ion-ios7-eye"></i></a>';
    }
}
function sharedir($share,$id){
    if($share == "1"){
        echo '<a href="share_dir.php?id='.$id.'"  target="_blank" class="btn btn-info" data-toggle="tooltip" data-container="body" data-placement="top" title="分享"><i class="ion-share"></i></a>';
        echo '<a href="#" class="btn btn-cos sharefile" data-share-id="'.$id.'" data-share-type="dir" data-toggle="tooltip" data-placement="top" data-container="body" title="取消公開資料夾"><i class="ion-ios7-upload-outline"></i></a>';
    }else{
        echo '<a href="#" class="btn btn-info" data-toggle="tooltip" data-container="body" data-placement="top" title="您尚未公開資料夾"><i class="ion-share"></i></a>';
        echo '<a href="#" class="btn btn-cos sharefile" data-share-id="'.$id.'" data-share-type="dir" data-toggle="tooltip" data-placement="top" data-container="body" title="分享資料夾"><i class="ion-ios7-upload-outline"></i></a>';
    }
}
function sharefile($share,$id){
    if($share == "1"){
        echo '<a href="downfile.php?id='.$id.'" target="_blank" class="btn btn-default" data-toggle="tooltip" data-container="body" data-placement="top" title="分享"><i class="ion-share"></i></a>';
        echo '<a href="#" class="btn btn-cos sharefile" data-share-id="'.$id.'" data-share-type="file" data-toggle="tooltip" data-placement="top" data-container="body" title="取消公開檔案"><i class="ion-ios7-upload-outline"></i></a>';
    }else{
        echo '<a href="#" class="btn btn-default" data-toggle="tooltip" data-container="body" data-placement="top" title="您尚未公開檔案"><i class="ion-share"></i></a>';
        echo '<a href="#" class="btn btn-cos sharefile" data-share-id="'.$id.'" data-share-type="file"  data-toggle="tooltip" data-placement="top" data-container="body" title="公開檔案"><i class="ion-ios7-upload-outline"></i></a>';
    }
}
function fileformat($type,$name){
    if(preg_match("/image\/(.*)/i", $type)){
        echo strtoupper(str_replace("image/", "", $type)).'圖檔';
    }elseif(preg_match("/audio\/(.*)/i", $type)){
        echo strtoupper(str_replace("audio/", "", $type)).'音樂檔';
    }elseif(preg_match("/video\/(.*)/i", $type)){
        echo strtoupper(str_replace("vedio/", "", $type)).'影片檔';
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
        echo substr($name,-(strlen($name)-strrpos($name, ".")));
    }
}
function dirsize($id){
    $total = 0;
    foreach($GLOBALS["db"]->select("file",array('owner' => $_SESSION["username"],'dir' => $id,'recycle'=>'0')) as $d){
        $total += $d["size"];
    }
    foreach($GLOBALS["db"]->select("dir",array('owner' => $_SESSION["username"],'parent' => $id,'recycle'=>'0')) as $d){
        $total += dirsize($d["id"]);
    }
    return $total;
}
$filecheck=$db->select("file",array('owner' => $_SESSION["username"],'dir'=>$_GET["dir"],'recycle'=>'0'));
$dircheck=$db->select("dir",array('owner' => $_SESSION["username"],'parent'=>$_GET["dir"],'recycle'=>'0'));
if ($filecheck[0]["id"]==NULL&&$dircheck[0]["id"]==NULL) echo '<div class="alert alert-info" style="margin-top: 80px;">您沒有任何檔案，去上傳一個吧！</div>';
else{?>
<table class="table" id="file-list">
    <thead class="table_header">
        <tr>
            <td style="width:30% !important;">檔名</td>
            <td style="max-width:10%; width: auto;">上傳時間</td>
            <td style="max-width:10%; width: auto;">檔案大小</td>
            <td style="max-width:10%; width: auto;">檔案類型</td>
            <td style="min-width:350px;">動作</td>
        </tr>
    </thead>
    <tbody>
<?php }
if($dircheck[0]["id"]!=NULL){
    foreach($db->select("dir",array('owner' => $_SESSION["username"],'parent' => $_GET["dir"],'recycle'=>'0')) as $d){ ?>
        <tr data-id="<?php echo $d['id']; ?>" data-type="dir" <?php if($d["color"]!="") echo " class='".$d["color"]."' "; ?>>
            <td><?php echo $d['name']; ?></td>
            <td style="max-width:100px; width: auto; word-break:break-all;"><?php echo $d['date']; ?></td>
            <td style="max-width:100px; width: auto; word-break:break-all;"><?php echo sizecount(dirsize($d["id"])/1000/1000); ?></td>
            <td>資料夾</td>
            <td style="min-width:350px;">
                <div class="btn-group file-actions" data-id="<?php echo $d['id']; ?>">
                    <a href="home.php?dir=<?php echo $d['id']; ?>" class="btn btn-default" data-toggle="tooltip" data-container="body" data-placement="top" title="開啟"><i class="ion-ios7-eye"></i></a>
                    <a href="ajax_recycle_dir.php" data-dir-delete-id="<?php echo $d['id']; ?>" class="btn btn-danger" data-toggle="tooltip" data-container="body" data-placement="top" title="刪除"><i class="ion-trash-b"></i></a>
                    <a href="ajax_rename_dir.php" data-dir-rename-id="<?php echo $d['id']; ?>" class="btn btn-cos" data-toggle="tooltip" data-container="body" data-placement="top" title="重新命名"><i class="ion-edit"></i></a>
                    <a href="ajax_move_dir.php" data-dir-move-id="<?php echo $d['id']; ?>" class="btn btn-success" data-toggle="tooltip" data-container="body" data-placement="top" title="移動"><i class="ion-clipboard"></i></a>
                    <?php @sharedir($d['share'],$d["id"]); ?>
                    <a href="ajax_set_color" data-dir-color-id="<?php echo $d['id']; ?>" class="btn btn-primary" data-toggle="tooltip" data-container="body" data-placement="top" title="標記"><i class="ion-bookmark"></i></a>
                </div>
            </td>
        </tr>
    <?php }
}
if($filecheck[0]["id"]!=NULL){
    foreach($db->select("file",array('owner' => $_SESSION["username"],'dir' =>  $_GET["dir"],'recycle'=>'0')) as $d){ ?>
        <tr data-id="<?php echo $d['id']; ?>" data-type="file" <?php if($d["color"]!="") echo " class='".$d["color"]."' "; ?>>
            <td><a href="downfile.php?id=<?php echo $d['id']; ?>&download=true"><?php echo $d['name']; ?></a></td>
            <td style="max-width:100px; width: auto; word-break:break-all;"><?php echo $d['date']; ?></td>
            <td style="max-width:100px; width: auto; word-break:break-all;"><?php echo sizecount($d['size']/1000/1000); ?></td>
            <td style="max-width:100px; width: auto; word-break:break-all;"><?php fileformat($d['type'],$d['name']); ?></td>
            <td style="min-width:350px;">
                <div class="btn-group file-actions" data-id="<?php echo $d['id']; ?>">
                    <?php @sharefile($d['share'],$d["id"]); ?>
                    <?php @linkcheck($d['type'],$d['id'],$d["name"]); ?>
                    <a href="ajax_recycle.php" data-delete-id="<?php echo $d['id']; ?>" class="btn btn-danger" data-toggle="tooltip" data-container="body" data-placement="top" title="刪除"><i class="ion-trash-b"></i></a>
                    <a href="ajax_rename.php" data-rename-id="<?php echo $d['id']; ?>" class="btn btn-primary" data-toggle="tooltip" data-container="body" data-placement="top" title="重新命名"><i class="ion-edit"></i></a>
                    <a href="ajax_move.php" data-move-id="<?php echo $d['id']; ?>" class="btn btn-success" data-toggle="tooltip" data-container="body" data-placement="top" title="移動"><i class="ion-clipboard"></i></a>
                    <?php @previewcheck($d['type'],$d['id']); ?>
                    <a href="ajax_set_color" data-color-id="<?php echo $d['id']; ?>" class="btn btn-cos" data-toggle="tooltip" data-container="body" data-placement="top" title="標記"><i class="ion-bookmark"></i></a>
                </div>
            </td>
        </tr>
    <?php }
}
if ($filecheck[0]["id"]!=NULL||$dircheck[0]["id"]!=NULL) echo '</tbody></table>';            