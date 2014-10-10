<?php include('config.php'); 
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
function linkcheck($type, $id){
    if(preg_match("/image\/(.*)/i", $type) || preg_match("/audio\/(.*)/i", $type)||preg_match("/video\/(.*)/i", $type) || $type == "application/pdf" || $type == "application/x-shockwave-flash" || $type == "text/html" || $type == "text/plain"){
        echo '<a href="readfile.php?id='.$id.'" target="_blank" class="btn btn-info">外連檔案</a>';
    }else{
        echo '<a href="#" class="btn btn-info disabled">外連檔案</a>';
    }
}
function previewcheck($type, $id){
    if(preg_match("/image\/(.*)/i", $type) || preg_match("/audio\/(.*)/i", $type)||preg_match("/video\/(.*)/i", $type)||$type == "text/html" || $type == "text/plain"){
        echo '<a href="#" data-preview-id="'.$id.'" class="btn btn-warning">預覽</a>';
    }else{
        echo '<a href="#" class="btn btn btn-warning disabled">預覽</a>';
    }
}
$filecheck=$db->select("file",array('owner' => $_SESSION["username"],'dir'=>$_SESSION["dir"]));
$dircheck=$db->select("dir",array('owner' => $_SESSION["username"],'parent'=>$_SESSION["dir"]));
if ($filecheck[0]["id"]==NULL&&$dircheck[0]["id"]==NULL) echo '<div class="alert alert-info" style="margin-top: 80px;">您沒有任何檔案，去上傳一個吧！</div>';
else{?>
<table class="table" id="file-list">
    <thead>
        <tr>
            <td>檔名</td>
            <td>上傳時間</td>
            <td>檔案類型</td>
            <td>動作</td>
        </tr>
    </thead>
    <tbody>
<?php }
if($dircheck[0]["id"]!=NULL){
    foreach($db->select("dir",array('owner' => $_SESSION["username"],'parent' => $_GET["dir"])) as $d){ ?>
        <tr data-id="<?php echo $d['id']; ?>" data-type="dir">
            <td><?php echo $d['name']; ?></td>
            <td><?php echo $d['date']; ?></td>
            <td>資料夾</td>
            <td>
                <div class="btn-group file-actions" data-id="<?php echo $d['id']; ?>">
                    <a href="home.php?dir=<?php echo $d['id']; ?>" class="btn btn-default">開啟</a>
                    <a href="ajax_delete_dir.php" data-dir-delete-id="<?php echo $d['id']; ?>" class="btn btn-danger">刪除</a>
                    <a href="ajax_rename_dir.php" data-dir-rename-id="<?php echo $d['id']; ?>" class="btn btn-primary">重新命名</a>
                    <a href="ajax_move_dir.php" data-dir-move-id="<?php echo $d['id']; ?>" class="btn btn-success">移動</a>
                    <a href="share_dir.php?id=<?php echo $d['id']; ?>" class="btn btn-info">分享</a>
                </div>
            </td>
        </tr>
    <?php }
}
if($filecheck[0]["id"]!=NULL){
    foreach($db->select("file",array('owner' => $_SESSION["username"],'dir' =>  $_GET["dir"])) as $d){ ?>
        <tr data-id="<?php echo $d['id']; ?>" data-type="file">
            <td><?php echo $d['name']; ?></td>
            <td><?php echo $d['date']; ?></td>
            <td><?php echo $d['type']; ?></td>
            <td>
                <div class="btn-group file-actions" data-id="<?php echo $d['id']; ?>">
                    <a href="downfile.php?id=<?php echo $d['id']; ?>" target="_blank" class="btn btn-default">下載</a>
                    <?php @linkcheck($d['type'],$d['id']); ?>
                    <a href="ajax_delete.php" data-delete-id="<?php echo $d['id']; ?>" class="btn btn-danger">刪除</a>
                    <a href="ajax_rename.php" data-rename-id="<?php echo $d['id']; ?>" class="btn btn-primary">重新命名</a>
                    <a href="ajax_move.php" data-move-id="<?php echo $d['id']; ?>" class="btn btn-success">移動</a>
                    <?php @previewcheck($d['type'],$d['id']); ?>
                </div>
            </td>
        </tr>
    <?php }
}
if ($filecheck[0]["id"]!=NULL||$dircheck[0]["id"]!=NULL) echo '</tbody></table>';            