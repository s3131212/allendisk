<?php
/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/

@set_time_limit(20);
include dirname(dirname(__FILE__)).'/config.php';
if (!session_id()) {
    session_start();
}

function sizecount($size)
{
    if ($size < 0.001) {
        return round(($size * 1000 * 1000), 2).'B';
    } elseif ($size >= 0.001 && $size < 1) {
        return round(($size * 1000), 2).'KB';
    } elseif ($size >= 1 && $size < 1000) {
        return round($size, 2).'MB';
    } elseif ($size >= 1000) {
        return round(($size / 1000), 2).'GB';
    }
}
function linkcheck($type, $id, $name, $secret)
{
    /* Decode Password */
    $passphrase['b'] = $_SESSION['password'];
    $passphrase['c'] = $secret;
    $iv = md5("\x1B\x3C\x58".$passphrase['b'], true).md5("\x1B\x3C\x58".$passphrase['b'], true);
    $key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 24);
    $passphrase['a'] = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($passphrase['c']), MCRYPT_MODE_CBC, $iv), "\0\3");
    if (preg_match("/image\/(.*)/i", $type) || preg_match("/audio\/(.*)/i", $type) || preg_match("/video\/(.*)/i", $type) || preg_match("/text\/(.*)/i", $type) || $type == 'application/pdf' || $type == 'application/x-shockwave-flash') {
        echo '<a href="readfile.php/'.$name.'?id='.$id.'&password='.$passphrase['a'].'" target="_blank" class="btn btn-info">外連檔案</a>';
    } else {
        echo '<a href="#" class="btn btn-info disabled">外連檔案</a>';
    }
}

function previewcheck($type, $id)
{
    if (preg_match("/image\/(.*)/i", $type) || preg_match("/audio\/(.*)/i", $type) || preg_match("/video\/(.*)/i", $type) || preg_match("/text\/(.*)/i", $type) || $type == 'application/msword' || $type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $type == 'application/vnd.ms-excel' || $type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $type == 'application/vnd.ms-powerpoint' || $type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
        echo '<a href="#" data-preview-id="'.$id.'" class="btn btn-warning">預覽</a>';
    } else {
        echo '<a href="#" class="btn btn btn-warning disabled">預覽</a>';
    }
}
function sharedir($share, $id)
{
    if ($share == '1') {
        echo '<a href="share_dir.php?id='.$id.'"  target="_blank" class="btn btn-info">分享</a>';
        echo '<a href="#" class="btn btn-cos sharefile" data-share-id="'.$id.'" data-share-type="dir">取消公開資料夾</a>';
    } else {
        echo '<a href="#" class="btn btn-info disabled">分享</a>';
        echo '<a href="#" class="btn btn-cos sharefile" data-share-id="'.$id.'" data-share-type="dir" >公開資料夾</a>';
    }
}
function sharefile($share, $id, $secret)
{
    if ($share == '1') {
        /* Decode Password */
        $passphrase['b'] = $_SESSION['password'];
        $passphrase['c'] = $secret;
        $iv = md5("\x1B\x3C\x58".$passphrase['b'], true).md5("\x1B\x3C\x58".$passphrase['b'], true);
        $key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 24);
        $passphrase['a'] = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($passphrase['c']), MCRYPT_MODE_CBC, $iv), "\0\3");

        echo '<a href="downfile.php?id='.$id.'&password='.$passphrase['a'].'" target="_blank" class="btn btn-default">分享</a>';
        echo '<a href="#" class="btn btn-cos sharefile" data-share-id="'.$id.'" data-share-type="file">取消公開檔案</a>';
    } else {
        echo '<a href="#" class="btn btn-default disabled">分享</a>';
        echo '<a href="#" class="btn btn-cos sharefile" data-share-id="'.$id.'" data-share-type="file">公開檔案</a>';
    }
}
function fileformat($type, $name)
{
    if (preg_match("/image\/(.*)/i", $type)) {
        echo strtoupper(str_replace('image/', '', $type)).' 圖檔';
    } elseif (preg_match("/audio\/(.*)/i", $type)) {
        echo strtoupper(str_replace('audio/', '', $type)).' 音樂檔';
    } elseif (preg_match("/video\/(.*)/i", $type)) {
        echo strtoupper(str_replace('vedio/', '', $type)).' 影片檔';
    } elseif (preg_match("/text\/(.*)/i", $type)) {
        echo '純文字檔';
    } elseif ($type == 'application/msword' || $type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
        echo 'MS Office Word';
    } elseif ($type == 'application/vnd.ms-excel' || $type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        echo 'MS Office Excel';
    } elseif ($type == 'application/vnd.ms-powerpoint' || $type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
        echo 'MS Office Powerpoint';
    } elseif ($type == 'application/x-bzip2' || $type == 'application/x-gzip' || $type == 'application/x-7z-compressed' || $type == 'application/x-rar-compressed' || $type == 'application/zip' || $type == 'application/x-apple-diskimage' || $type == 'application/x-tar') {
        echo '壓縮檔';
    } else {
        echo strtoupper(substr($name, -(strlen($name) - strrpos($name, '.') - 1))).'檔';
    }
}

function fileicon($type, $name)
{
    if (preg_match("/image\/(.*)/i", $type)) {
        echo '<i class="fa fa-file-image-o fa-4x"></i>';
    } elseif (preg_match("/audio\/(.*)/i", $type)) {
        echo '<i class="fa fa-file-sound-o fa-4x"></i>';
    } elseif (preg_match("/video\/(.*)/i", $type)) {
        echo '<i class="fa fa-file-movie-o fa-4x"></i>';
    } elseif (preg_match("/text\/(.*)/i", $type)) {
        echo '<i class="fa fa-file-text-o fa-4x"></i>';
    } elseif ($type == 'application/msword' || $type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
        echo '<i class="fa fa-file-word-o fa-4x"></i>';
    } elseif ($type == 'application/vnd.ms-excel' || $type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        echo '<i class="fa fa-file-excel-o fa-4x"></i>';
    } elseif ($type == 'application/vnd.ms-powerpoint' || $type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
        echo '<i class="fa fa-file-powerpoint-o fa-4x"></i>';
    } elseif ($type == 'application/x-bzip2' || $type == 'application/x-gzip' || $type == 'application/x-7z-compressed' || $type == 'application/x-rar-compressed' || $type == 'application/zip' || $type == 'application/x-apple-diskimage' || $type == 'application/x-tar') {
        echo '<i class="fa fa-file-zip-o fa-4x"></i>';
    } else {
        echo '<i class="fa fa-file-o fa-4x">';
    }
}

function dirsize($id)
{
    $total = 0;
    foreach ($GLOBALS['db']->select('file', array('owner' => $_SESSION['username'], 'dir' => $id, 'recycle' => '0')) as $d) {
        $total += $d['size'];
    }
    foreach ($GLOBALS['db']->select('dir', array('owner' => $_SESSION['username'], 'parent' => $id, 'recycle' => '0')) as $d) {
        $total += dirsize($d['id']);
    }

    return $total;
}
$filecheck = $db->select('file', array('owner' => $_SESSION['username'], 'dir' => $_SESSION['dir'], 'recycle' => '0'));
$dircheck = $db->select('dir', array('owner' => $_SESSION['username'], 'parent' => $_SESSION['dir'], 'recycle' => '0'));
if ($filecheck[0]['id'] == null && $dircheck[0]['id'] == null) {
    echo '<div class="alert alert-info" style="margin-top: 80px;">您沒有任何檔案，去上傳一個吧！</div>';
} else {
    ?>
<?php echo '<div class="row">';
    if ($dircheck[0][ 'id'] != null) {
        foreach ($db->select('dir', array('owner' => $_SESSION['username'], 'parent' => $_SESSION['dir'], 'recycle' => '0')) as $d) {
            ?>
<div class="col-lg-3 col-md-4 col-sm-6">
	<div class="panel" data-id="<?php echo $d['id'];
            ?>" data-type="dir" data-download-url="home.php?dir=<?php echo $d['id'];
            ?>">
		<div>
            <div class="panel-body"><i class="fa fa-4x fa-fw fa-folder"></i>
            </div>
			<div class="panel-footer <?php if ($d['color'] != '') {
    echo 'tag-'.$d['color'];
}
            ?>">
				<span><?php echo $d[ 'name'];
            ?></span>
			</div>
            <div class="file-action" style="display: none;">
                <a href="home.php?dir=<?php echo $d['id'];
            ?>" class="btn btn-default">開啟</a>
                <a href="ajax_recycle_dir.php" data-dir-delete-id="<?php echo $d['id'];
            ?>" class="btn btn-danger">刪除</a>
                <a href="ajax_rename_dir.php" data-dir-rename-id="<?php echo $d['id'];
            ?>" class="btn btn-cos">重新命名</a>
                <a href="ajax_move_dir.php" data-dir-move-id="<?php echo $d['id'];
            ?>" class="btn btn-success">移動</a>
                <?php @sharedir($d['share'], $d['id']);
            ?>
                <a href="ajax_set_color" data-dir-color-id="<?php echo $d['id'];
            ?>" class="btn btn-primary">標記</a>
            </div>
		</div>
	</div>
</div>
<?php 
        }
    }
    echo '</div><div class="row">';
    if ($filecheck[0][ 'id'] != null) {
        foreach ($db->select('file', array('owner' => $_SESSION['username'], 'dir' => $_SESSION['dir'], 'recycle' => '0')) as $d) {
            ?>
<div class="col-lg-3 col-md-4 col-sm-6">
	<div class="panel" data-id="<?php echo $d['id'];
            ?>" data-type="file" data-download-url="downfile.php?id=<?php echo $d['id'];
            ?>&download=true">
		<div class="panel-body"><?php fileicon($d['type'], $d['name']);
            ?></i>
		</div>
		<div class="panel-footer<?php if ($d['color'] != '') {
    echo ' tag-'.$d['color'];
}
            ?>"><?php echo $d[ 'name'];
            ?></div>
        <div class="file-action" style="display: none;">
            <?php @sharefile($d['share'], $d['id'], $d['secret']);
            ?>
            <?php @linkcheck($d['type'], $d['id'], $d['name'], $d['secret']);
            ?>
            <a href="ajax_recycle.php" data-delete-id="<?php echo $d['id'];
            ?>" class="btn btn-danger">刪除</a>
            <a href="ajax_rename.php" data-rename-id="<?php echo $d['id'];
            ?>" class="btn btn-primary">重新命名</a>
            <a href="ajax_move.php" data-move-id="<?php echo $d['id'];
            ?>" class="btn btn-success">移動</a>
            <?php @previewcheck($d['type'], $d['id']);
            ?>
            <a href="ajax_set_color" data-color-id="<?php echo $d['id'];
            ?>" class="btn btn-cos">標記</a>
        </div>
	</div>
</div>
<?php 
        }
    }
    echo '</div>';
}
exit();
