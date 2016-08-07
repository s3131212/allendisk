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
<style>body{ background-color: #F8F8F8; }</style>
</head>
<body>
<div class="container">
  <h1 class="text-center"><?php echo $config['sitetitle']; ?> 管理介面</h1>
    <ul class="nav nav-tabs">
        <li><a href="index.php">管理介面首頁</a></li>
        <li><a href="setting.php">設定</a></li>
        <li><a href="newuser.php">新增使用者</a></li>
        <li><a href="manuser.php">管理使用者</a></li>
        <li class="active"><a href="#">頁面</a></li>
        <li><a href="../index.php">回到首頁</a></li>
        <li><a href="login.php">登出</a></li>
    </ul>
    <?php
        if(isset($_GET['success']) && $_GET['success'] == 'edit'){
            echo "<div class='alert alert-success'>編輯成功</div>";
        }elseif(isset($_GET['success']) && $_GET['success'] == 'delete'){
            echo "<div class='alert alert-success'>刪除成功</div>";
        } 
    ?>
    <a class="btn" href="manpage.php?id=new">新增</a>
    <table class="table">
        <thead>
            <tr>
                <td>ID</td>
                <td>標題</td>
                <td>動作</td>
            </tr>
        </thead>
        <tbody>
            <?php
                $pages = $db->select('page');
                if (is_array($pages)) {
                    foreach ($pages as $d) {
            ?>
                        <tr>
                            <td><?php echo $d['id'] ?></td>
                            <td><?php echo $d['title'] ?></td>
                            <td>
                                <a class="btn btn-info" href="manpage.php?id=<?php echo $d['id'] ?>">編輯</a>
                            </td>
                        </tr> 
            <?php

                    }
                }
            ?>
        </tbody>
    </table>
</div>
<p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
</body>
</html>
