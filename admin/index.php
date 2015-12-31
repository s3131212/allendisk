<?php
/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
require '../config.php';
if (!session_id()) {
    session_start();
}

if (!$_SESSION['alogin']) {
    header('location:login.php');
    exit();
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
$used = $db->ExecuteSQL(sprintf('SELECT SUM(`size`) AS `sum` FROM `file`'));
$filecount = $db->ExecuteSQL(sprintf('SELECT COUNT(`id`) AS `count` FROM `file`'));
$sharecount = $db->ExecuteSQL(sprintf("SELECT COUNT(`id`) AS `count` FROM `file` WHERE `share` = '1'"));
$usercount = $db->ExecuteSQL(sprintf('SELECT COUNT(`name`) AS `count` FROM `user`'));

//Check Update
$current_version = '1.6.0';
$newest_version = @file_get_contents('http://ad.allenchou.cc/version.txt');
//$newest_version = '1.5.1'; //development only
if (version_compare($newest_version, $current_version, '>')) {
    $update = true;
} else {
    $update = false;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>管理員介面 - <?php echo $config['sitename'];?></title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <style>
        body{
            background-color: #F8F8F8;
        }
        p {
            line-height: 30px;
        }
        .num{
            font-size: 20px;
            line-height: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center"><?php echo $config['sitetitle']; ?> 管理介面</h1>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#">管理介面首頁</a></li>
            <li><a href="setting.php">設定</a></li>
            <li><a href="newuser.php">新增使用者</a></li>
            <li><a href="manuser.php">管理使用者</a></li>
            <li><a href="../index.php">回到首頁</a></li>
            <li><a href="login.php">登出</a></li>
        </ul>
        <br />
        <?php
        if ($update) {
            echo '<div class="alert alert-warning">新版 Allen Disk 已經發表，請儘速至 <a href="http://ad.allenchou.cc" target="_blank">Allen Disk 官網</a> 下載新版，謝謝</div>';
        }
        ?>
        <div class='row'>
          <div class="panel panel-default col-md-4 col-md-offset-1">
              <div class="panel-heading">
                  <h3 class="panel-title">檔案</h3>
              </div>
              <div class="panel-body">
                  <p>檔案數：<span class='num'><?php echo $filecount[0]['count']; ?> 個</span></p>
                  <p>佔用空間：<span class='num'><?php echo sizecount(($used[0]['sum'] / 1000 / 1000)); ?></span></p>
                  <p>公開分享檔案數：<span class='num'><?php echo $sharecount[0]['count']; ?> 個</span></p>
              </div>
          </div>
          <div class="panel panel-default col-md-4 col-md-offset-2">
              <div class="panel-heading">
                  <h3 class="panel-title">用戶</h3>
              </div>
              <div class="panel-body">
                  <p>用戶數：<span class='num'><?php echo $usercount[0]['count']; ?> 位</span></p>
                  <p>總可用空間：<span class='num'><?php echo ($config['total'] != 0) ? sizecount($usercount[0]['count'] * $config['total'] / 1000 / 1000) : '無限制'; ?></span></p>
              </div>
          </div>
      </div>
  </div>
</br>
<p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
</body>
</html>
