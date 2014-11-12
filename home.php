<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include('config.php'); 
if(!session_id()) session_start();
if(!$_SESSION["login"]){
    header("Location:login.php");
    exit();
}
function sizecount($size){
    if ($size<0.001) {
        return round(($size*1000*1000), 2) . "B";
    }elseif ($size>=0.001 &&$size < 1) {
        return round(($size*1000), 2) . "KB";
    }elseif ($size>=1 &&$size < 1000) {
        return round($size, 2) . "MB";
    }elseif ($size >= 1000) {
        return round(($size/1000), 2) . 'GB';
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

function create_used_bar($total){
    $audio = 0; $video = 0; $image = 0; $other = 0;
    foreach($GLOBALS["db"]->select("file",array('owner' => $_SESSION["username"])) as $d){
        if(preg_match("/image\/(.*)/i", $d["type"])) $image+=$d["size"];
        elseif(preg_match("/audio\/(.*)/i", $d["type"])) $audio+=$d["size"];
        elseif(preg_match("/video\/(.*)/i", $d["type"])) $video+=$d["size"];
        else $other+=$d["size"];
    }
    $output = '<div class="progress progress-striped">';
    $used_list = '<ul class="list-unstyled">';
    if($audio != 0){
        $output .= '<div class="progress-bar progress-bar-success"  role="progressbar" style="width:'.round(($audio/1000/1000/$total*100),1).'%;"></div>';
        $used_list .= '<li class="text-success">音樂： '.sizecount(($audio/1000/1000)).' ( '.round(($audio/1000/1000/$total*100),1).'% )</li>';
    }else $used_list .= '<li class="text-success">音樂： 0MB ( 0% )</li>';
    if($video != 0){
        $output.= '<div class="progress-bar progress-bar-info"  role="progressbar" style="width:'.round(($video/1000/1000/$total*100),1).'%;"></div>';
        $used_list .= '<li class="text-info">影片： '.sizecount(($video/1000/1000)).' ( '.round(($video/1000/1000/$total*100),1).'% )</li>';
    }else $used_list .= '<li class="text-info">影片： 0MB ( 0% )</li>';
    if($image != 0){
        $output.= '<div class="progress-bar progress-bar-warning"  role="progressbar" style="width:'.round(($image/1000/1000/$total*100),1).'%;"></div>';
        $used_list .= '<li class="text-warning">照片： '.sizecount(($image/1000/1000)).' ( '.round(($image/1000/1000/$total*100),1).'% )</li>';
    }else $used_list .= '<li class="text-warning">照片： 0MB ( 0% )</li>';
    if($other != 0){
        $output.= '<div class="progress-bar"  role="progressbar" style="width:'.round(($other/1000/1000/$total*100),1).'%;"></div>';
        $used_list .= '<li>其他： '.sizecount(($other/1000/1000)).' ( '.round(($other/1000/1000/$total*100),1).'% )</li>';
    }else $used_list .= '<li>其他： 0MB ( 0% )</li>';
    $output .= "</div>";
    $used_list .= "</ul>";
    echo $output.$used_list;
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
$used = $db->ExecuteSQL(sprintf('SELECT SUM(`size`) AS `sum` FROM `file` WHERE `owner` = \'%s\' AND `recycle` = \'0\'',mysql_real_escape_string($_SESSION["username"])));
$used = $used[0]['sum']; //這是MB
$info = $db->select("user",array('name' => $_SESSION["username"]));
if(isset($_GET["dir"])&&$_GET["dir"]!=null) $_SESSION["dir"] = $_GET["dir"];
else header("location: home.php?dir=0");
if($_GET["dir"]!="0"){
    $dir = $db->select("dir",array('id' => $_GET["dir"],'owner'=>$_SESSION["username"]));
    if($dir[0]["name"]==null){
        header("location: home.php?dir=0");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $config["sitename"];?></title>
    <meta charset="utf-8" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="http://code.ionicframework.com/ionicons/1.5.2/css/ionicons.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script>var dir = '<?php echo $_SESSION["dir"]; ?>';</script>
    <script src="js/modernizr.js" charset="utf-8"></script>
    <script src="js/script.js" charset="utf-8"></script>
    <script>
        <?php if($config["updatesec"]!=0){ ?>
        var update = true;
        $(function(){
            setInterval(function(){
                if(update===true){
                    updateList();
                    updateUsage();
                }
            }, <?php echo ($config["updatesec"]*1000); ?>);
            
            //update = false;
            //for debugging
        });
        <?php } ?>
    </script>
    <?php if($_GET["harlem"]=="shake"){ //easter egg for 1.4 ?>
    <script>
    update = false;
    (function(c){var b=c.getElementsByTagName("head")[0];a=c.createElement("script");a.type="text/javascript";a.src=unescape("%2F%2Fdl.dropboxusercontent.com%2Fs%2F82526m7wt4vpesr%2Fharlem-shake.js");a.async=true;b.appendChild(a);})(document);
    </script>
    <?php } ?>
    <style>
        body{
            background-color: #F8F8F8;
            height:100%;
        }
        .main-content {
            overflow: hidden;
            margin: 1em 5px;
        }
        .dir-tree {
            list-style: none;
            padding: 1em 0.5em;
            margin: 0;
        }
        .main-tree > ul > li {
            background: #FFF;
            border-left: 3px solid #F39C12;
            margin: 5px 0;
            box-shadow: 1px 1px 2px #DDD;
        }
        .dir-tree li {
            padding: 0.5em 1em;
        }
        
        .dir-tree li:hover {
        }
        
        .dir-tree ul.dir-tree {
            padding: 0;
            margin-left: 0.25em;
        }
        
        .tree-indicator {
            font-weight: bold;
        }
        
        .dir-tree ul.dir-tree {
            margin-top: 0.5em;
            margin-left: 0.5em;
            padding-left: 0.5em;
            border-left: 1px #EEE solid;
            border-bottom: none;
        }
        
        .dir-tree ul.dir-tree li {
            padding: 0;
        }
        .tree_selected{
            color:#0038ff;
        }
        .tree_selected:hover{
            color:#325fff;
        }
        .dragover_box{
            background-color: #f8f8f8 !important;
        }
        .btn-cos{
            background-color:#62615f;
            color: #fff;
        }
        .btn-cos:hover{
            background-color:#494847;
            color: #fff;
        }
        .info td{
            background-color: rgba(52,152,219,0.8) !important;
        }
        .success td{
            background-color: rgba(24,188,156,0.8) !important;
        }
        .warning td{
            background-color: rgba(243,156,18,0.8) !important;
        }
        .danger td{
            background-color: rgba(231,76,60,0.8) !important;
        }
        @media (min-width: 992px) {
            .nav-container{
                width:100%;
            }
            #file-nav{
                position: fixed;
                top:0;
                left: 0;
                right: 0;
                z-index: 200;
                background-color: rgba(240, 240, 240, 0.9);
            }
            #sidebar{
                position: fixed;
                top:70px;
                left: 0;
                right: 0;
                z-index: 100;
            }
            #main_div{
                height:100%;
                overflow: auto;
                float:right;
            }
            .table_header{
                font-size:20px;
                line-height: 50px;
                text-align:center;
            }
        }
        @media (max-width: 991px) {
            .nav-container{
                width:100%;
            }
            #file-nav{
                background-color: rgba(240, 240, 240, 0.9);
            }
            #sidebar{
                
            }
            #main_div{
                overflow: auto;
                width: 100%;
            }
            .table_header{
                font-size:15px;
                line-height: 16px;
                text-align:center;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
<div class="row">
    <div class="col-md-2" id="sidebar">
        <ul class="nav nav-stacked nav-pills nav-side">
            <h1 class="text-center"><?php echo $config["sitetitle"]; ?></h1>
            <li class="active"><a href="home.php?dir=0">首頁</a></li>
            <li><a href="#" id="info-btn">帳號資訊</a></li>
            <li><a href="#" id="recycle-btn">回收桶</a></li>
            <li><a href="logout.php">登出</a></li>
        </ul>
        <div id="usage_box">
            <p>已經使用<?php echo sizecount($config["total"]); ?>中的<?php echo sizecount(($used/1000/1000)); ?> ( <?php echo round($used/1000/1000/$config["total"]*100,2); ?>% )</p>
            <?php create_used_bar($config["total"]); ?>
        </div>
        <p class="text-center text-info">Proudly Powered by <br /> <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
    </div>
    <div class="nav-container">
        <div id="file-nav">
            <div class="pull-left btn-group" style="margin:15px; display:none;" id="action_btn_multi">
                <button class="btn btn-danger" type="button" id="delete-btn">刪除</button>
                <button class="btn btn-default" type="button" id="move-btn">移動</button>
                <button class="btn btn-primary" type="button" id="tag-btn">標記</button>
                <button class="btn btn-info" type="button" id="select-all-btn">全選</button>
            </div>
            <div class="pull-right btn-group" style="margin:15px;" id="action_btn">
                <?php if($_SESSION["dir"] != "0"){
                    $updir=$db->select("dir",array('id' => $_SESSION["dir"]));
                    echo '<a class="btn btn-info" href="home.php?dir='.$updir[0]["parent"].'">上一層</a>' ;
                } ?>
                <button class="btn btn-warning" type="button" id="multi-btn">批量管理</button>
                <button class="btn btn-primary" type="button" id="upload-btn">上傳檔案</button>
                <button class="btn btn-success" type="button" id="mkdir-btn">新增資料夾</button>
            </div>
        </div>
    </div>
    <div class="col-md-10" id="main_div">
        <div id="file_list_container" style="margin-bottom:70px; margin-top:80px;">
            <?php 
                $filecheck=$db->select("file",array('owner' => $_SESSION["username"],'dir'=>$_SESSION["dir"],'recycle'=>'0'));
                $dircheck=$db->select("dir",array('owner' => $_SESSION["username"],'parent'=>$_SESSION["dir"],'recycle'=>'0'));
                if ($filecheck[0]["id"]==NULL&&$dircheck[0]["id"]==NULL) echo '<div class="alert alert-info" style="margin-top: 80px;">您沒有任何檔案，去上傳一個吧！</div>';
                else{ ?>
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
                    <?php if($dircheck[0]["id"]!=NULL){ 
                        foreach($db->select("dir",array('owner' => $_SESSION["username"],'parent' => $_SESSION["dir"],'recycle'=>'0')) as $d){ ?>
                        <tr data-id="<?php echo $d['id']; ?>" data-type="dir" <?php if($d["color"]!="") echo " class='".$d["color"]."' "; ?>>
                            <td><?php echo $d['name']; ?></td>
                            <td style="max-width:100px; width: auto; word-break:break-all;"><?php echo $d['date']; ?></td>
                            <td style="max-width:100px; width: auto; word-break:break-all;"><?php echo sizecount(dirsize($d["id"])/1000/1000); ?></td>
                            <td style="max-width:100px; width: auto; word-break:break-all;">資料夾</td>
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
                    } if($filecheck[0]["id"]!=NULL){ ?>
                    <?php foreach($db->select("file",array('owner' => $_SESSION["username"],'dir' => $_SESSION["dir"],'recycle'=>'0')) as $d){ ?>
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
                    <?php } }?>
                </tbody>
            </table>
            <?php } ?>
        </div>
    </div>
    <div id="rename-file" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="update = true;">&times;</button>
                    <h3 id="myModalLabel">重新命名檔案</h3>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <div class="input-group">
                                <input id="rename-filename" type="text" placeholder="檔案名稱"  class="form-control" />
                                <div class="input-group-addon" id="rename-file-ext"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true" onclick="update = true;">取消</button>
                    <button class="btn btn-primary" id="rename-file-btn">重新命名</button>
                </div>
            </div>
        </div>
    </div>
    <div id="upload-file" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="update = true;">&times;</button>
                    <h3 id="myModalLabel">上傳檔案</h3>
                </div>
                <div class="modal-body">
                    <p>上傳限制：<?php echo sizecount(($config["total"])); ?>，已用空間：<?php sizecount(($used/1000/1000)); ?> (<?php echo round(($used/1000/1000/$config["total"]*100),1); ?> %)當您上傳檔案，代表您已經同意<a href="tos.php"  target="_blank">使用條款</a>了，如果您不同意，請勿上傳任何檔案</p>
                    <p>如果您的瀏覽器較為老舊，或是要上傳非常多個檔案，請使用傳統上傳</p>
                    <p class="text-center"><a href="#" class="btn btn-info" id="ajax_upload_btn">拖曳上傳</a>&nbsp;<a href="#" class="btn btn-info" id="traditional_upload_btn">傳統上傳</a>&nbsp;<a href="#" class="btn btn-info" id="remote_upload_btn">遠端上傳</a></p>
                    <div id="upload_box" style="width:100%; height:200px; background-color:#eeeeee; -webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;">
                        <div style="margin:0 auto; width:50%; font-size:25px; text-align:center; padding:50px;">
                            <span>拖拉到此上傳</span>
                            <br /><span style="font-size:15px;">或</span><br />
                            <div style="height:44px; width:94px; margin:0 auto;">
                                <button class="btn primary" id="upload_button">瀏覽檔案</button>
                                <input id="file" name="file[]" type="file" style="opacity:0 ;margin-top: -44px;width: 93px;height: 44px;" multiple></br>
                            </div>
                        </div>
                    </div>
                    <div class="progress" id="upload_progress_box" style="margin-top:20px;">
                        <div class="progress-bar progress-bar-striped active" id="upload_progress" style="width: 0%"> 0% </div>
                    </div>
                    <div id="upload_table_box">
                        <table class="table">
                            <tbody id="upload_table">
                                <tr>
                                    <td>檔案名稱</td>
                                    <td>檔案大小</td>
                                    <td>上傳結果</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <iframe src="uploadiframe.php" style="border:none; width:100%; height:200px; display:none;" id="upload_iframe">您的瀏覽器暫時不支援 iframe </iframe>
                    <iframe src="remotedownload.php" style="border:none; width:100%; height:200px; display:none;" id="remote_iframe">您的瀏覽器暫時不支援 iframe </iframe>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true" onclick="update = true;">關閉</button>
                </div>
            </div>
        </div>
    </div>
    <div id="preview-file" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="update = true;">&times;</button>
                    <h3 id="myModalLabel">預覽檔案</h3>
                </div>
                <div class="modal-body embed-responsive embed-responsive-4by3">
                    <iframe src="preview.php" id="preview-iframe" style="border:none; width:100%; height:100%;">您的瀏覽器暫時不支援 iframe </iframe>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true" onclick="update = true;">關閉</button>
                </div>
            </div>
        </div>
    </div>
    <div id="rename-dir" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="update = true;">&times;</button>
                    <h3 id="myModalLabel">重新命名資料夾</h3>
                </div>
                <div class="modal-body">
                    <form>
                      <div class="form-group">
                        <input id="rename-dirname" type="text" placeholder="資料夾名稱" class="form-control" />
                      </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true" onclick="update = true;">取消</button>
                    <button class="btn btn-primary" id="rename-dir-btn">重新命名</button>
                </div>
            </div>
        </div>
    </div>
    <div id="info-modal" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="update = true;">&times;</button>
                    <h3 id="myModalLabel">帳號資訊</h3>
                </div>
                <div class="modal-body">
                    <table class="table table-hover">
                        <tr>
                            <td>帳號</td>
                            <td><?php echo $_SESSION["username"]?></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><?php echo $info[0]["email"]?></td>
                        </tr>
                        <tr>
                            <td>加入時間</td>
                            <td><?php echo $info[0]["jointime"]?></td>
                        </tr>
                        <tr>
                            <td>已使用的空間</td>
                            <td><?php echo sizecount(($used/1000/1000)); ?> (<?php echo round(($used/1000/1000/$config["total"]*100),1); ?> %)</td>
                        </tr>
                    </table>
                    <iframe src="setpass.php" style="border:none; width:100%; height:300px;">您的瀏覽器暫時不支援 iframe ，請使用 <a href="setpass.php">此連結</a> 修改</iframe>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true" onclick="update = true;">關閉</button>
                </div>
            </div>
        </div>
    </div>
    <div id="mkdir-modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="update = true;">&times;</button>
                    <h3 id="myModalLabel">新增資料夾</h3>
                </div>
                <div class="modal-body">
                    <iframe src="mkdir.php" style="border:none; width:100%; height:200px;">您的瀏覽器暫時不支援 iframe ，請使用 <a href="mkdir.php">此連結</a> 新增</iframe>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true" onclick="update = true;">關閉</button>
                </div>
            </div>
        </div>
    </div>
    <div id="mvfile-modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="update = true;">&times;</button>
                    <h3 id="myModalLabel">移動檔案</h3>
                </div>
                <div class="modal-body">
                    <ul class="dir-tree">
                        <li><span class="tree-indicator"><i class="ion-ios7-minus-empty"></i></span>
                            <a href="#" class="filetreeselector" data-id="0">主目錄</a><ul class="dir-tree" id="ajax_load_tree"></ul>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="move-submit-btn">移動檔案</button>
                    <button class="btn" data-dismiss="modal" aria-hidden="true" onclick="update = true;">關閉</button>
                </div>
            </div>
        </div>
    </div>
    <div id="recycle-box" class="modal fade">
        <div class="modal-lg modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="update = true;">&times;</button>
                    <h3 id="myModalLabel">回收筒</h3>
                </div>
                <div class="modal-body">
                    <button class="btn" id="real_delete_all">清空</button>
                    <table class="table">
                        <thead>
                            <tr>
                            <td>檔案名稱</td>
                            <td>檔案類型</td>
                            <td>原始位置</td>
                            <td>動作</td>
                            </tr>
                        </thead>
                        <tbody id="recycle_list">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true" onclick="update = true;">關閉</button>
                </div>
            </div>
        </div>
    </div>
    <div id="color-tag" class="modal fade">
        <div class="modal-lg modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="update = true;">&times;</button>
                    <h3 id="myModalLabel">顏色標注</h3>
                </div>
                <div class="modal-body">
                    <button type="button" class="btn btn-lg btn-block color-tag-btn" id="">無色</button>
                    <button type="button" class="btn btn-default btn-lg btn-block color-tag-btn color-tag-btn" id="active">灰色</button>
                    <button type="button" class="btn btn-success btn-lg btn-block color-tag-btn" id="success">綠色</button>
                    <button type="button" class="btn btn-info btn-lg btn-block color-tag-btn" id="info">水藍</button>
                    <button type="button" class="btn btn-warning btn-lg btn-block color-tag-btn" id="warning">黃色</button>
                    <button type="button" class="btn btn-danger btn-lg btn-block color-tag-btn" id="danger">紅色</button>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true" onclick="update = true;">關閉</button>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>