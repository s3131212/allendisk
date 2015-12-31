<?php
/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include 'config.php';
function check_dir($id)
{
    $dir = $GLOBALS['db']->select('dir', array('id' => $id));
    if ($dir[0]['recycle'] == '1' || $dir[0]['share'] == '0') {
        return false;
    }
    if ($dir[0]['parent'] != '0') {
        $updir = $GLOBALS['db']->select('dir', array('id' => $dir[0]['parent']));
        if ($updir[0]['recycle'] == '1' || $updir[0]['share'] == '0') {
            return false;
        } else {
            return check_dir($updir[0]['id']);
        }
    } else {
        return true;
    }
}
function fileformat($type, $name)
{
    if (preg_match("/image\/(.*)/i", $type)) {
        echo strtoupper(str_replace('image/', '', $type)).' 圖檔';
    } elseif (preg_match("/audio\/(.*)/i", $type)) {
        echo strtoupper(str_replace('audio/', '', $type)).' 音樂檔';
    } elseif (preg_match("/video\/(.*)/i", $type)) {
        echo strtoupper(str_replace('video/', '', $type)).' 影片檔';
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
if (!session_id()) {
    session_start();
}
$dir = $db->select('dir', array('id' => $_GET['id']));
$dircheck = $db->select('dir', array('owner' => $dir[0]['owner'], 'parent' => $_GET['id'], 'recycle' => '0', 'share' => '1'));
$filecheck = $db->select('file', array('owner' => $dir[0]['owner'], 'dir' => $_GET['id'], 'recycle' => '0', 'share' => '1'));
if (!check_dir($_GET['id'])) {
    $alert = "<div class='alert alert-warning'>此資料夾不存在或是被設定為不公開</div>";
    $dir[0]['name'] = '空白資料夾';
} else {
    $alert = '';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/bootstrap.min.js"></script>
    <title><?php echo $dir[0]['name'] ?> - <?php echo $config['sitename'] ?></title>
</head>
<body>
    <div class="container">
        <h1 class="text-center"><?php echo $config['sitetitle']; ?></h1>
        <?php
        if ($_SESSION['login']) {
            ?>
            <ul class="nav nav-tabs">
                <li><a href="index.php">首頁</a></li>
                <li><a href="logout.php">登出</a></li>
            </ul>
        <?php 
        } else {
            ?>
            <ul class="nav nav-tabs">
                <li><a href="index.php">首頁</a></li>
                <?php if ($config['why']) {
    ?><li><a href="why.php"><?php echo $config['sitename'];
    ?>的好處</a></li><?php 
}
            ?>
                <li><a href="login.php">登入</a></li>
                <?php if ($config['reg']) {
    ?><li><a href="reg.php">註冊</a></li><?php 
}
            ?>
                <?php if ($config['tos']) {
    ?><li><a href="tos.php">使用條款</a></li><?php 
}
            ?>
            </ul>
        <?php 
        } ?>
        <h2>檢視<?php echo $dir[0]['name'] ?></h2>
        <?php echo $alert; ?>
        <table class="table">
            <thead>
                <tr>
                    <td>檔名</td>
                    <td>檔案類型</td>
                    <td>動作</td>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($alert == '') {
                if ($dircheck[0]['id'] != null) {
                    foreach ($dircheck as $d) {
                        ?>
                        <tr>
                            <td><?php echo $d['name'];
                        ?></td>
                            <td>資料夾</td>
                            <td><a href="share_dir.php?id=<?php echo $d['id'];
                        ?>" class="btn btn-default">開啟</a></td>
                        </tr>
            <?php 
                    }
                }
                if ($filecheck[0]['id'] != null) {
                    foreach ($filecheck as $d) {
                        ?>
                        <tr>
                            <td><?php echo $d['name'];
                        ?></td>
                            <td><?php fileformat($d['type'], $d['name']);
                        ?></td>
                            <td><a href="downfile.php?id=<?php echo $d['id'];
                        ?>" target="_blank" class="btn btn-default">下載</a></td>
                        </tr>
            <?php 
                    }
                }
            } ?>
            </tbody>
        </table>
        <p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
    </div>
</body>
</html>
